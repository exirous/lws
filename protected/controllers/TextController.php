<?php

class TextController extends Controller
{

    public function actionItem()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('id', 0, AHttpRequest::PARAM_TYPE_NUMERIC);
                require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
                $parser = new JBBCode\Parser();
                $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
                $text = Text::model()->findByPk($id)->getRenderAttributes();
                $parser->parse($text['text']);
                $text['text'] = nl2br($parser->getAsHTML());
                $this->returnSuccess($text);
                break;
            default:
                $this->returnError();
        }
    }

    public function actionEdit()
    {
        $request = Yii::app()->request;
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->canMakeOrders())
            return null;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_POST:
                $text = $request->getRequiredRawBodyParam('text', '', AHttpRequest::PARAM_TYPE_STRING);
                $title = $request->getRequiredRawBodyParam('title', '', AHttpRequest::PARAM_TYPE_STRING);
                $id = $request->getRequiredRawBodyParam('id', 0, AHttpRequest::PARAM_TYPE_NUMERIC);
                $this->returnSuccess($this->_saveText($id, $title, $text));
                break;
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('id', 0, AHttpRequest::PARAM_TYPE_NUMERIC);
                $text = Text::model()->findByPk($id)->getRenderAttributes();
                $this->returnSuccess($text);
                break;
            default:
                $this->returnError();
        }
    }

    public function _saveText($id, $title, $textString)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            if ($id > 0)
                $text = Text::model()->findByPk($id);
            else
                $text = new Text();

            $text->text = $textString;
            $text->title = $title;
            if (!$text->save())
                throw new Exception($text->getErrorsString());
            $transaction->commit();
            return $text->getRenderAttributes();
        } catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return null;
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