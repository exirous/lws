<?php

class TeamSpeakController extends Controller
{
    var $layout = "json";

    public function actions()
    {
        Yii::t('forms', 'cate');
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class'     => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page'    => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionViewTree()
    {
        $content = ['tree' => []];
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

                    $clientNode = ['name' => $client['client_nickname']->toString(), 'groups' => []];
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
                    'name'     => $channel->toString(),
                    'channels' => getRecursiveList($channel->subChannelList(), false, $knownGroups),
                    'clients'  => $clients
                ];

                if (count($channelNode['clients']) || count($channelNode['channels']))
                    $tree[] = $channelNode;
            }
            return $tree;
        }

        $knownGroups     = [];
        $content['tree'] = getRecursiveList(Yii::app()->ts->channelList(), true, $knownGroups);

        $this->render('//common/json', compact('content'));
    }

    public function actionViewHtmlTree()
    {
        echo Yii::app()->ts->ts3Server->getViewer(new TeamSpeak3_Viewer_Html("images/viewericons/", "images/countryflags/", "data:image"));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public
    function actionError()
    {
        if ($error = Yii::app()->errorHandler->error)
        {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }
}