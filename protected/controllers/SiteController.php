<?php

class SiteController extends Controller
{
    /**
     * Declares class-based actions.
     */

    public function actions()
    {
        Yii::t('forms','cate');
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }


    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        $this->renderText('');
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

      /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm']))
        {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();

        $this->redirect(Yii::app()->homeUrl);
    }


    public function actionTest()
    {
        //Yii::beginProfile('blockID');

        mailer::send('luftwaffeschule@gmail.com','Новый пользователь','<html>Зарегистрирован новый пользователь: <a href="http://lws.exirous.com/#/user/roster/1">ASD</a></html>');

        echo ini_get('memory_limit').'!!';

        //$value = Property::model()->findAll();
        //Yii::app()->cache->set('testCache', $value);
        //var_dump(Yii::app()->cache->get('testCache'));
        /**
         * @var Property[] $model
         * */
        //$model = $value;
        //$model = Yii::app()->cache->get('testCache');
        //Yii::endProfile('blockID');
        /*foreach ($model as $property)
        {
            echo $property->view_id.'!<br>';
        }*/
        //Yii::app()->cache->delete('testCache');
        //die(var_dump(Yii::app()->cache->get('testCache')));
    }


}