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
                $this->returnSuccess($this->_addNews($title, $text));
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

    private function _addNews($title,$text)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            News::add($title,$text);
            $transaction->commit();
        } catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
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