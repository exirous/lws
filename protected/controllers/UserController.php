<?php

class UserController extends Controller
{
    var $layout = "json";

    public function actions()
    {
        return array(
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionView()
    {
        $user = User::model()->findByPk(Yii::app()->request->getQuery('id'));
        $content['user'] = [];
        if ($user)
        {
            $content['user'] = ['nickname' => $user->nickname, 'name' => $user->fistname, 'id' => $user->id];
        }
        $this->render('//common/json', compact('content'));
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