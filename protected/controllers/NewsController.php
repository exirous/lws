<?php

class NewsController extends Controller
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



    public function actionItem()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_POST:
                $text = $request->getRequiredRawBodyParam('text','',AHttpRequest::PARAM_TYPE_STRING);
                $title = $request->getRequiredRawBodyParam('title','',AHttpRequest::PARAM_TYPE_STRING);
                $id = $request->getRawBodyParam('id',0,AHttpRequest::PARAM_TYPE_NUMERIC);
                $onlyForRegistered = $request->getRawBodyParam('onlyRegistered', false);
                $this->returnSuccess($this->_saveNews($id, $title, $text, $onlyForRegistered));
                break;
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('id', 0, AHttpRequest::PARAM_TYPE_NUMERIC);
                $this->returnSuccess($this->_getNews($id));
                break;
            default:
                $this->returnError();
        }
    }




    public function actionLast()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess(News::getLast());
                break;
            default:
                $this->returnError();
        }
    }

    public function actionLastOrders()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess(Order::getLast());
                break;
            default:
                $this->returnError();
        }
    }

    private function _saveNews($id, $title,$text, $onlyForRegistered)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->canMakeNews())
            return $this->returnError("No permission");
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            if ($id) {
                $news = News::model()->findByPk($id);
                if (!$news)
                    $this->returnError('No news');
                $news->title = $title;
                $news->text = $text;
                $news->only_for_registered = $onlyForRegistered ? 1 : 0;
                if (!$news->save())
                    throw new Exception($news->getErrorsString());
            }
            else
                $news = News::add($title, $text, $onlyForRegistered);
            $transaction->commit();
            return $news->editAttributes();
        } catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _getNews($id)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->canMakeNews())
            return $this->returnError("No permission");

        return News::model()->findByPk($id)->editAttributes();
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