<?php

class MessengerController extends Controller
{
    const MESSAGE_LIMIT = 10;
    public function actionConversation()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('senderId', 0);
                $this->returnSuccess($this->_getConversation($id));
                break;
            default:
                $this->returnError();
        }
    }


    public function actionPostMessage()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_POST:
                $recieverId = $request->getRequiredRawBodyParam('recieverId', 0);
                if ($recieverId == Yii::app()->user->id)
                    $this->returnError();
                $text = $request->getRequiredRawBodyParam('text', '');
                $this->returnSuccess($this->_postMessage($recieverId, $text));
                break;
            default:
                $this->returnError();
        }
    }


    public function actionConversationPage()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('senderId', 0);
                $page = $request->getRequiredParam('page', 1);
                $this->returnSuccess($this->_getConversationPage($id, $page));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionList()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess($this->_getConversations());
                break;
            default:
                $this->returnError();
        }
    }


    public function actionTest()
    {

    }


    public function _getConversations()
    {
        require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
        $parser = new JBBCode\Parser();
        $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
        $messages = $messages2 = [];
        $messagesTemp = PrivateMessage::model()->findAll(['condition' => 'sender_id=:senderId', 'params' => ['senderId' => Yii::app()->user->model->id], 'order' => 't.id desc']);
        foreach ($messagesTemp as $message)
        {
            if (isset($messages[$message->reciever_id]))
                continue;
            $attributes = $message->getRenderAttributes();
            $messages[$message->reciever_id] = [
                'sender'=>$message->reciever,
                'lastMessage'=>$attributes
            ];
        }
        $messagesTemp = PrivateMessage::model()->findAll(['condition' => 'reciever_id=:recieverId', 'params' => ['recieverId' => Yii::app()->user->model->id], 'order' => 't.id desc']);
        foreach ($messagesTemp as $message)
        {
            if (isset($messages2[$message->sender_id]))
                continue;
            $messages2[$message->sender_id] = true;
            $attributes = $message->getRenderAttributes();
            if (!isset($messages[$message->sender_id]) || ($messages[$message->sender_id]['lastMessage']['time'] < $attributes['time']))
            {
                $messages[$message->sender_id] = [
                    'sender' => $message->sender,
                    'lastMessage' => $attributes
                ];
            }
        }
        $messages = array_values($messages);
        return $messages;
    }


    public function _getConversation($id)
    {
        $sender = User::model()->findByPk($id);
        if (!$sender)
            $this->returnError();
        $topic = [
            'limit' => self::MESSAGE_LIMIT,
            'sender'=> $sender->getListAttributes(),
            'itemCount'=> PrivateMessage::model()->scopeMeAndHim($sender->id)->count()
        ];
        return $topic;
    }

    public function _getConversationPage($id, $page)
    {
        require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
        $parser = new JBBCode\Parser();
        $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
        $messageModels = PrivateMessage::model()
            ->scopeMeAndHim($id)
            ->scopeOrder('id desc')
            ->scopeLimit(self::MESSAGE_LIMIT)
            ->scopeOffset(($page - 1) * self::MESSAGE_LIMIT)->findAll();
        $messages = [];
        foreach($messageModels as $message) {
            $attributes = $message->getRenderAttributes();
            $parser->parse($attributes['text']);
            $attributes['text'] = nl2br($parser->getAsText());
            $messages[] = $attributes;
            if (($message->reciever_id == Yii::app()->user->model->id) && !$message->is_read)
            {
                $message->is_read = 1;
                $message->save();
            }
        }
        return $messages;
    }

    public function _postMessage($recieverId, $text)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            if (Yii::app()->user->isGuest)
                throw new Exception('Guests cannot post messages!');
            $reciever  = User::model()->findByPk($recieverId);
            if (!$reciever)
                throw new Exception('User not found!');

            $message = new PrivateMessage();
            $message->reciever_id = $reciever->id;
            $message->sender_id = Yii::app()->user->model->id;
            $message->text = $text;
            if (!$message->validate())
                throw new Exception($message->getErrorsString());
            if (!$message->save())
                throw new Exception('Error while saving');
            $transaction->commit();
            $message->notify();

            require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
            $parser = new JBBCode\Parser();
            $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
            $messageAttributes = $message->getRenderAttributes();
            $parser->parse($messageAttributes['text']);
            $messageAttributes['text'] = nl2br($parser->getAsHTML());
            return $messageAttributes;
        } catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
            return null;
        }
    }

    public function _addTopic($title, $text)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            if (Yii::app()->user->isGuest)
                throw new Exception('Guests cannot post topics!');

            $topic = new ForumTopic();
            $topic->title = $title;
            $topic->author_id = Yii::app()->user->model->id;
            if (!$topic->validate())
                throw new Exception($topic->getErrorsString());
            if (!$topic->save())
                throw new Exception('Error while saving topic');

            $message = new ForumMessage();
            $message->topic_id = $topic->id;
            $message->author_id = Yii::app()->user->model->id;
            $message->text = $text;
            if (!$message->validate())
                throw new Exception('1:'.$message->getErrorsString());
            if (!$message->save())
                throw new Exception('Error while saving');

            $topic->last_message_id = $message->id;
            $topic->first_message_id = $message->id;
            if (!$topic->save())
                throw new Exception('Error while saving topic');
            $transaction->commit();
            return $topic->getRenderAttributes();
        } catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
            return null;
        }
    }


    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
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