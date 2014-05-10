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

        // URL on which we have to post data
        $url = "http://192.168.1.4:3001";

        // Any other field you might want to post
        $json_data = json_encode(array("name"=>"PHP Rockstart", "age"=>29));
        $post_data['json_data'] = $json_data;
        $post_data['secure_hash'] = mktime();

        // Initialize cURL
        $ch = curl_init();
        // Set URL on which you want to post the Form and/or data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));
        // Pass TRUE or 1 if you want to wait for and catch the response against the request made
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // For Debug mode; shows up any error encountered during the operation
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // Execute the request
        $response = curl_exec($ch);
        $error = curl_error ($ch);
        // Just for debug: to see response
        var_dump($error);
        echo '<br>';
        die(var_dump($response));

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

        //Yii::app()->ts->channelTree2();
        //die();

        //$db = Yii::app()->ts->ts3Server->clientGetServerGroupsByDbid(2);
        //$db = Yii::app()->ts->ts3Server->clientList();
        /*foreach ($db as $key => $client)
        {
            //$client->message('Здорово! Мы потихонечку переходим на новый сайт и переносим туда все данные. НО, данные о регистрации перенести муторно, поэтому прошу регистрироватся заново на новом сайте (заодно можете назвать другой адрес электронной почты и пароль, по желанию)  [url]http://lws.exirous.com/#/roster[/url] Спасибо за внимание! :)');
            //if (stripos($client['client_nickname']->toString(), 'exirous') === false) continue;
            //echo '!=' . $key . ':' . nl2br(print_r($client['clid'], true)) . '<br>';
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