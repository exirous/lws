<?php
Yii::import('application.vendors.*');
require_once('TeamSpeak3/TeamSpeak3.php');

class TeamSpeak extends CApplicationComponent
{

    /**
     * @var TeamSpeak3_Node_Server $ts3Server
     * */
    public $ts3Server;
    public $server_ip = '127.0.0.1';
    public $server_port = '9987';
    public $connectionString = 'serverquery://serveradmin:5329@127.0.0.1:10011/?server_port=9987';
    public $isBlocking = true;

    public function init()
    {
        parent::init();
        Yii::registerAutoloader(array('TeamSpeak3', 'autoload'));
        $this->connect($this->isBlocking);
    }

    private function connect($blocking)
    {
        try
        {
            if (file_exists(dirname(Yii::app()->basePath) . '/protected/data/ts_connection' . ($blocking ? '' : '_noblock')))
                $this->ts3Server = unserialize(file_get_contents(dirname(Yii::app()->basePath) . '/protected/data/ts_connection' . ($blocking ? '' : '_noblock')));
            if (!$this->ts3Server)
            {
                $this->ts3Server = TeamSpeak3::factory($this->connectionString . ($blocking ? '' : '&blocking=0'));
                file_put_contents(dirname(Yii::app()->basePath) . '/protected/data/ts_connection' . ($blocking ? '' : '_noblock'), serialize($this->ts3Server));
            }
        } catch (TeamSpeak3_Adapter_ServerQuery_Exception $e)
        {
            $this->ts3Server = false;
        }
    }


    /**
     * @return TeamSpeak3_Node_Client[]
     * */
    public function clientList()
    {
        return $this->ts3Server->clientList();
    }

    public function clientListDb()
    {
        return $this->ts3Server->clientListDb();
    }

    public function groupList()
    {
        return $this->ts3Server->serverGroupList();
    }


    public function setName($name)
    {
        $this->ts3Server->execute('clientupdate client_nickname=' . str_replace(' ', '\s', $name) . '');
    }

    /**
     * @return Array[]
     */
    public function channelTree()
    {
        /**
         * @param $channels TeamSpeak3_Node_Channel[]
         */
        function getRecursiveList($channels, $isTop, &$knownGroups)
        {
            $tree = [];
            foreach ($channels as $channel)
            {
                if ($isTop && $channel->getLevel() > 0)
                    continue;
                /**
                 * @var $client TeamSpeak3_Node_Client
                 */
                $clients = [];
                foreach ($channel->clientList() as $client)
                {
                    if ($client['client_type'])
                        continue;
                    $user = User::model()->find(['condition' => 'ts_id=:id', 'params' => [':id' => $client['client_unique_identifier']]]);

                    $clientNode = ['name' => $client['client_nickname']->toString(), 'groups' => []];
                    if ($user)
                    {
                        $clientNode['id'] = $user->id;
                        $clientNode['is_clanner'] = intval($user->is_clanner);
                    }
                    /**
                     * @var $group TeamSpeak3_Node_Servergroup
                     */
                    foreach (explode(",", $client["client_servergroups"]) as $sgid)
                    {
                        if (!isset($knownGroups[$sgid]))
                            $knownGroups[$sgid] = $client->getParent()->serverGroupGetById($sgid);
                        $group = $knownGroups[$sgid];
                        if ($group["iconid"])
                        {
                            if (!file_exists(dirname(Yii::app()->basePath) . '/img/groups/' . $sgid . '.png'))
                                file_put_contents(dirname(Yii::app()->basePath) . '/img/groups/' . $sgid . '.png', $group->iconDownload()->toString());
                            $clientNode['groups'][] = ['name' => $group->toString(), 'id' => $sgid];
                        }
                    }
                    $clients[] = $clientNode;
                }

                $channelNode = [
                    'name' => $channel->toString(),
                    'channels' => getRecursiveList($channel->subChannelList(), false, $knownGroups),
                    'clients' => $clients
                ];

                if (count($channelNode['clients']) || count($channelNode['channels']))
                    $tree[] = $channelNode;
            }
            return $tree;
        }

        $knownGroups = [];
        return getRecursiveList($this->ts3Server->channelList(), true, $knownGroups);
    }

    public function cleanUserDb()
    {
        $from = 0;
        do {
            $db = $this->ts3Server->clientListDb($from, 200);
            foreach ($db as $client) {
                $uid = $client['client_unique_identifier']->toString();
                if (!User::model()->findByAttributes(['ts_id'=>$uid]))
                  $this->ts3Server->clientDeleteDb($client['cldbid']);
            }
            $from+=200;
        } while (count($db) == 200);
    }

    public function findUsersLike($nickname, $ip)
    {
        $from = 0;
        $users = [];
        do {
            try {
                $db = $this->ts3Server->clientListDb($from, 200);
            }
            catch (\Exception $exception)
            {
                $db = [];
            }
            foreach ($db as $client) {
                $name = (isset($client['client_nickname']) && method_exists($client['client_nickname'], "toString")) ? $client['client_nickname']->toString() : '';
                $lastIp = (isset($client['client_lastip']) && method_exists($client['client_lastip'], "toString")) ? $client['client_lastip']->toString() : '';
                if (stripos($name, $nickname) !== false || ($ip && ($ip == $lastIp))) {
                    $groups = array_keys(Yii::app()->ts->ts3Server->clientGetServerGroupsByDbid($client["cldbid"]));
                    $uid = $client['client_unique_identifier']->toString();
                    $users[$uid] = [
                        'id' => $uid,
                        'name' => $name,
                        'byName' => $ip != $lastIp,
                        'byIp' => $ip == $lastIp,
                        'isOnline' => false,
                        'serverGroups' => $groups,
                        'lastOnline' => intval($client['client_lastconnected'])
                    ];
                }
            }
            $from += 200;
        } while (count($db) == 200);
        $db = $this->clientList();

        foreach ($db as $client) {
            $client = $client->getInfo();

            if ($client["client_type"])
                continue;
            $groups = explode(',', $client["client_servergroups"]);

            $lastIp = $client['connection_client_ip'] ? $client['connection_client_ip']->toString() : '';
            $name = $client['client_nickname'] ? $client['client_nickname']->toString() : '';
            if (stripos($name, $nickname) === false && levenshtein(preg_replace('/\(.*?\)/', '', $name), $nickname) > 5)
                continue;
            $uid = $client['client_unique_identifier']->toString();
            $users[$uid] = [
                'id' => $uid,
                'name' => $name,
                'byName' => false,
                'byIp' => $ip == $lastIp,
                'isOnline' => true,
                'serverGroups' => $groups,
                'lastOnline' => time(),
            ];
        }
        $users = array_values($users);
        usort($users, function($a, $b){
            return $a['lastOnline'] - $b['lastOnline'];
        });

        return $users;
    }

    public function checkToken($token)
    {
        if (!$token) return false;
        try
        {
            $tokenList = array_keys($this->ts3Server->tokenList());
        } catch (TeamSpeak3_Adapter_ServerQuery_Exception $e)
        {
            return false;
        }
        return in_array($token, $tokenList);
    }

    public function notifyInstructors()
    {
        $clients = $this->clientList();
        foreach ($clients as $client)
        {
            $isInstructor = false;
            $groups = explode(',', $client["client_servergroups"]);
            foreach (['9', '27', '28'] as $group)
                if (in_array($group, $groups))
                {
                    $isInstructor = true;
                    break;
                }
            if ($isInstructor)
                $client->poke("Уважаемый инструктор! вас ожидают в приёмной!");
        }
    }

    public function generateNormalToken($forName = "")
    {
        $token = $this->ts3Server->tokenCreate(0, $this->getServerGroupIdByName('Пользователь'), 0, 'Пользователь: ' . $forName);
        return $token;
    }

    public function generateAdminToken($forName = "")
    {
        $token = $this->ts3Server->tokenCreate(0, $this->getServerGroupIdByName('Администратор'), 0, 'Администратор: ' . $forName);
        return $token;
    }

    private function getServerGroupIdByName($name)
    {
        $serverGroup = $this->ts3Server->serverGroupGetByName($name);
        return $serverGroup->getId();
    }

    public function getDb($offset = 0)
    {
        return $this->ts3Server->clientListDb($offset, 200);
    }

}

?>