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