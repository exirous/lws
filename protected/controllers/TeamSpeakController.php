<?php

class TeamSpeakController extends Controller
{
    public function actions()
    {
    }

    public function actionViewTree()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess([]);
                break;
            default:
                $this->returnError();
        }
    }

    public function actionRedirect($id)
    {
        $user = User::model()->findByPk($id);
        if ($user)
            $this->redirect('ts3server://luftwaffeschule.ru/?nickname=' . urlencode($user->nickname . ' (' . $user->firstname . ')'), true);
        else
            $this->redirect('http://luftwaffeschule.ru', true);
    }

    public function actionIFrame()
    {
        $this->layout = 'iframe';
        $this->renderText('');
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

    function actionEmptyIcon($id){
        $this->layout = false;
        header('Content-Type:image/png');
        readfile('img/1.png');
        Yii::app()->end();
    }

}