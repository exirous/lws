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

    public function init()
    {
        parent::init();

        try
        {
            Yii::registerAutoloader(array('TeamSpeak3', 'autoload'));
            $this->ts3Server = TeamSpeak3::factory($this->connectionString);
        } catch (TeamSpeak3_Adapter_ServerQuery_Exception $e)
        {
            $this->ts3Server = false;
        }
    }

    public function clientList()
    {
        return $this->ts3Server->clientList();
    }

    public function clientListDb()
    {
        return $this->ts3Server->clientListDb();
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
                    if (strpos($client['client_nickname']->toString(), 'serveradmin') !== false)
                        continue;

                    $user = User::model()->find(['condition' => 'ts_id=:id', 'params' => [':id' => $client->getUniqueId()]]);

                    $clientNode = ['name' => $client['client_nickname']->toString(), 'groups' => []];
                    if ($user)
                        $clientNode['id'] = $user->id;
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

    public function maintance()
    {
        $warcraftClasses = WarcraftClass::model()->findAll();
        foreach ($warcraftClasses as $warcraftClass)
        {
            try
            {
                $serverGroup = $this->ts3Server->serverGroupGetByName($warcraftClass->name);
            } catch (TeamSpeak3_Adapter_ServerQuery_Exception $e)
            {
                $serverGroupId = $this->ts3Server->serverGroupCreate($warcraftClass->name);
                $serverGroup = $this->ts3Server->serverGroupGetById($serverGroupId);
            }
        }

        foreach ($this->ts3Server->clientListDb() as $client_id => $client)
        {
            $clint_nickname = $client['client_nickname'];

            $character = Character::model()->findByName($clint_nickname);

            if ($character)
            {
                $serverGroupName = $character->warcraftClass->name;
                try
                {
                    $this->ts3Server->serverGroupClientAdd($this->getServerGroupIdByName($serverGroupName), $client_id);
                } catch (TeamSpeak3_Adapter_ServerQuery_Exception $e)
                {
                }

            }
        }
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

}

?>