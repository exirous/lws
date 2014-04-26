<?php

class TeamSpeakController extends Controller
{
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
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess(Yii::app()->ts->channelTree());
                break;
            default:
                $this->returnError();
        }
    }

    public function actionTest()
    {
        //Yii::app()->ts->fixName();

        /*$user   = User::model()->findByPk(1);

        try
        {
            $client = Yii::app()->ts->ts3Server->clientGetByUid($user->ts_id);
        }
        catch(Exception $e)
        {

        }*/

        //mb_send_mail("exirous@gmail.com","test","test");

        //$db = Yii::app()->ts->getDb();



        //echo Yii::app()->user->model->ts_id;


        //$db = Yii::app()->ts->ts3Server->clientFindDb(Yii::app()->user->model->ts_id,true);
        //echo nl2br(print_r(Yii::app()->ts->ts3Server->clientInfoDb($db[0]), true));

        /*$db = Yii::app()->ts->ts3Server->clientGetServerGroupsByDbid(2);
        foreach ($db as $key => $client)
        {
            //if (stripos($client['client_nickname']->toString(), 'exirous') === false) continue;
            echo '!=' . $key . ':' . nl2br(print_r($client, true)) . '<br>';
        }*/


        //Order::model()->findByPk(3)->postToTeamSpeak();
        //Order::model()->findByPk(4)->postToTeamSpeak();
        //Yii::app()->user->model->syncWithTeamSpeak();

        //die(var_dump($db[126]));
        //Yii::app()->ts->ts3Server->serverGroupClientDel(7,129);


        //$client->poke("Поздравляем!");
        //$client->message("[COLOR=red]Server[B]Test[/B]!  Тестирую возможности сервера, не обращайте внимание. (Жека) :)[/COLOR]");

        //Yii::app()->ts->ts3Server->messageCreate($user->ts_id,'Тест','Тестируем');

        /**
         * @var $client TeamSpeak3_Node_Client
         */

        /*foreach (Yii::app()->ts->ts3Server->channelList() as $channel)
        {
            //$info = $channel->getInfo();
            //var_dump($info['cid']);
            //echo $info['cid'].':'.$info['channel_name']->toString().'<br>';
        }*/

        /*foreach (Yii::app()->ts->ts3Server->clientList() as $client)
        {
            if ($client["client_type"]) continue;
            var_dump($client);
            echo '<br><br><br>';
            /*if ($client["client_type"]) continue;
                $client->message("[COLOR=red]Server[B]Test[/B]!  Тестирую возможности сервера, не обращайте внимание. (Жека) :)[/COLOR]");*/
        //}*/
        //Yii::app()->ts->ts3Server->message("[COLOR=red]Server[B]Test[/B]!  Тестирую возможности сервера, не обращайте внимание. (Жека) :)[/COLOR]");
        //Yii::app()->ts->ts3Server->clientGetById(20)->message("[COLOR=red]your client is [B]outdated[/B]... update to [U]ASD[/U] now![/COLOR]");
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