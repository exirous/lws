<?php

class DaemonCommand extends CConsoleCommand
{
    static $doubleEvents = [];

    public static function needPreventEventDouble($data)
    {
        $hash = sha1(serialize($data));
        if (array_key_exists($hash, self::$doubleEvents))
        {
            if (self::$doubleEvents[$hash] < microtime(true) - 1200)
            {
                unset(self::$doubleEvents[$hash]);
                return false;
            }
            else
            {
                unset(self::$doubleEvents[$hash]);
                return true;
            }
        }
        else
        {
            self::$doubleEvents[$hash] = microtime(true);
            return false;
        }
    }


    public function actionReceptionMonitor()
    {
        Yii::app()->ts->setName("LuftWaffeSchule");
        Yii::app()->ts->ts3Server->notifyRegister("textprivate");
        Yii::app()->ts->ts3Server->notifyRegister("channel");
        // define a callback function
        function onTextMessage(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
        {
            //var_dump($event->getData());
            if ($event["msg"] == '!позвать')
            {
                $client = false;
                try
                {
                    Yii::app()->ts->ts3Server->clientListReset();
                    $client = Yii::app()->ts->ts3Server->clientGetByUid($event['invokeruid']);
                }
                catch (Exception $e)
                {
                    echo "Error: " . $event['invokeruid'] . " " . $e->getMessage();
                }
                if ($client)
                {
                    sleep(1);
                    Yii::app()->ts->notifyInstructors();
                    $client->message("[COLOR=red][B]Спасибо![/B][/COLOR] [COLOR=blue]Все инструктора были информированны о вашем прибытии, возможно вам прийдётся подождать! :)[/COLOR]");
                }
            }
        }

        function onClientMoved(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
        {
            $data = $event->getData();
            if (DaemonCommand::needPreventEventDouble($data))
                return;

            if ($data['ctid'] != 23)
                return;
            $client = false;

            try
            {
                Yii::app()->ts->ts3Server->clientListReset();
                $client = Yii::app()->ts->ts3Server->clientGetById($data['clid']);
            }
            catch (Exception $e)
            {
                echo "Error: " . $data['clid'] . " - " . $e->getMessage();
            }
            if ($client)
            {
                if ($client["client_servergroups"] == '8')
                {
                    sleep(1);
                    $client->message("[COLOR=red][B]Здравствуйте![/B][/COLOR] [COLOR=blue]Вы хотите информировать инструкторов о вашем прибытии?\n Ответьте прямо сдесь, написав комманду [/COLOR][COLOR=darkgreen]!позвать[/COLOR]");
                }
            }
        }

        function onClientEnter(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
        {
            $data   = $event->getData();
            $client = false;
            try
            {
                Yii::app()->ts->ts3Server->clientListReset();
                $client = Yii::app()->ts->ts3Server->clientGetById($data['clid']);
            }
            catch (Exception $e)
            {
                echo "Error: " . $data['clid'] . " - " . $e->getMessage();
            }
            if ($client)
            {
                if ($client["client_servergroups"] == '8')
                {
                    sleep(1);
                    $client->poke("Здравствуйте Гость! Прочитайте пожалуйста личное сообщение! :)");
                    $client->message("[COLOR=red][B]Здравствуйте![/B][/COLOR] [COLOR=blue]Если вы хотите вступить в наши ряды, то пройдите в [COLOR=black]Приёмную[/COLOR] и следуйте дальнейшим инструкциям :) [/COLOR]");
                }
            }
        }

        TeamSpeak3_Helper_Signal::getInstance()->subscribe("notifyClientmoved", "onClientMoved");
        TeamSpeak3_Helper_Signal::getInstance()->subscribe("notifyTextmessage", "onTextMessage");
        TeamSpeak3_Helper_Signal::getInstance()->subscribe("notifyCliententerview", "onClientEnter");
        // wait for events
        while (1)
        {
            Yii::app()->ts->ts3Server->getAdapter()->wait();
        }
    }

    /*public function actionGuestRoomMonitor()
    {
        Yii::app()->ts->fixName(" Гостевая");
        $your_own_client_id = Yii::app()->ts->ts3Server->whoamiGet("client_id");
        $target_channel_obj = Yii::app()->ts->ts3Server->channelGetByName("Гостевая");
        Yii::app()->ts->ts3Server->notifyRegister("channel", $target_channel_obj->getId());
        echo 'Registered!' . "\n";


        // wait for events
        echo "Starting to wait..." . "\n";
        while (1)
        {
            Yii::app()->ts->ts3Server->getAdapter()->wait();
        }
    }*/

}

