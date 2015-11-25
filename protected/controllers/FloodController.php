<?php

class FloodController extends Controller
{
    const MESSAGE_LIMIT = 12;

    public function actionTopic()
    {

        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('topicId', 0);
                $this->returnSuccess($this->_getTopic($id));
                break;
            case AHttpRequest::METHOD_POST:
                $text = $request->getRequiredRawBodyParam('text', '', AHttpRequest::PARAM_TYPE_STRING);
                $title = $request->getRequiredRawBodyParam('title', '', AHttpRequest::PARAM_TYPE_STRING);
                $this->returnSuccess($this->_addTopic($title, $text));
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
                $topicId = $request->getRequiredRawBodyParam('topicId', 0);
                $text = $request->getRequiredRawBodyParam('text', '');
                $this->returnSuccess($this->_postMessage($topicId, $text));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionDeleteTopic()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_POST:
                $topicId = $request->getRequiredRawBodyParam('id', 0);
                $this->returnSuccess($this->_deleteTopic($topicId));
                break;
            default:
                $this->returnError();
        }
    }


    public function actionTopicPage()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('topicId', 0);
                $page = $request->getRequiredParam('page', 1);
                $this->returnSuccess($this->_getTopicPage($id, $page));
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
                $this->returnSuccess($this->_getTopics());
                break;
            default:
                $this->returnError();
        }
    }


    public function actionTest()
    {

    }


    public function _getTopics()
    {
        require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
        $parser = new JBBCode\Parser();
        $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());

        $topics = [];
        foreach (ForumTopic::model()->findAll() as $topic)
        {
            $topicAttributes = $topic->getRenderAttributes();
            $parser->parse($topicAttributes['firstMessageText']);
            $topicAttributes['firstMessageText'] = mb_substr($parser->getAsText(),0,220,'utf-8');
            $topics[] = $topicAttributes;
        }
        return $topics;
    }


    public function _getTopic($id)
    {
        $topic = ForumTopic::model()->findByPk($id);
        if (!$topic)
            $this->returnError();
        $topic = $topic->getRenderAttributes();
        $topic['limit'] = self::MESSAGE_LIMIT;
        return $topic;
    }

    public function _getTopicPage($id, $page)
    {
        require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
        $parser = new JBBCode\Parser();
        $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
        $messageModels = ForumMessage::model()->scopeByTopic($id)->scopeLimit(self::MESSAGE_LIMIT)->scopeOffset(($page - 1) * self::MESSAGE_LIMIT)->findAll();
        $messages = [];
        foreach ($messageModels as $message)
        {
            $messageAttributes = $message->getRenderAttributes();
            $parser->parse($messageAttributes['text']);
            $messageAttributes['text'] = nl2br($parser->getAsHTML());
            $messages[] = $messageAttributes;
        }
        return $messages;
    }

    public function _postMessage($topicId, $text)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            if (Yii::app()->user->isGuest)
                throw new Exception('Guests cannot post messages!');
            $topic  = ForumTopic::model()->findByPk($topicId);
            if (!$topic)
                throw new Exception('Topic not found!');

            $message = new ForumMessage();
            $message->topic_id = $topic->id;
            $message->author_id = Yii::app()->user->model->id;
            $message->text = $text;
            if (!$message->validate())
                throw new Exception($message->getErrorsString());
            if (!$message->save())
                throw new Exception('Error while saving');

            $topic->last_message_id = $message->id;

            if (!$topic->save())
                throw new Exception('Error while saving topic');
            $transaction->commit();

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

    private function _deleteTopic($topicId)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            if (Yii::app()->user->isGuest || (Yii::app()->user->model->id != 1 && Yii::app()->user->model->id != 14))
                throw new Exception('You cant delete topics!');

            $topic  = ForumTopic::model()->findByPk($topicId);
            if (!$topic)
                throw new Exception('Topic not found!');

            $topic->first_message_id = null;
            $topic->last_message_id = null;
            $topic->save();

            foreach ($topic->forumMessages as $message)
                $message->delete();

            $topic->delete();
            $transaction->commit();
            return "OK";
        } catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
            return null;
        }
    }
}