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
        $content = ['tree' => Yii::app()->ts->channelTree()];
        $this->render('//common/json', compact('content'));
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

        $db = Yii::app()->ts->getDb();
        foreach ($db as $key => $client)
        {
            if (stripos($client['client_nickname']->toString(), 'exirous') === false) continue;
            echo '!=' . $key . ':' . nl2br(print_r($client, true)) . '<br>';
        }

        //die(var_dump($db[126]));
        //Yii::app()->ts->ts3Server->serverGroupClientDel(7,129);


        //$client->poke("Поздравляем!");
        //$client->message("[COLOR=red]Server[B]Test[/B]!  Тестирую возможности сервера, не обращайте внимание. (Жека) :)[/COLOR]");

        //Yii::app()->ts->ts3Server->messageCreate($user->ts_id,'Тест','Тестируем');

        /**
         * @var $client TeamSpeak3_Node_Client
         */
        /*foreach (Yii::app()->ts->ts3Server->clientList() as $client)
        {
            if ($client["client_type"]) continue;
                $client->message("[COLOR=red]Server[B]Test[/B]!  Тестирую возможности сервера, не обращайте внимание. (Жека) :)[/COLOR]");
        }*/
        //Yii::app()->ts->ts3Server->message("[COLOR=red]Server[B]Test[/B]!  Тестирую возможности сервера, не обращайте внимание. (Жека) :)[/COLOR]");
        //Yii::app()->ts->ts3Server->clientGetById(20)->message("[COLOR=red]your client is [B]outdated[/B]... update to [U]ASD[/U] now![/COLOR]");
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