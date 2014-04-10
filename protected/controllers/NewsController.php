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



    public function actionLast()
    {
        $request = Yii::app()->request;
        //$action = $request->getRequiredParam('action', 'none', AHttpRequest::PARAM_TYPE_STRING);
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess(News::getLast());
                break;
            default:
                $this->returnError();
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