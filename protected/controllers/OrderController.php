<?php

class OrderController extends Controller
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


    public function actionLogin()
    {
        $request = Yii::app()->request;
        $email = $request->getRequiredRawBodyParam('email', null, AHttpRequest::PARAM_TYPE_STRING);
        $password = $request->getRequiredRawBodyParam('password', null, AHttpRequest::PARAM_TYPE_STRING);
        switch ($request->method)
        {
            case AHttpRequest::METHOD_POST:
                $this->returnSuccess($this->_loginUser($email, $password));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionLogout()
    {
        try
        {
            $request = Yii::app()->request;
            switch ($request->method)
            {
                case AHttpRequest::METHOD_POST:
                    $this->returnSuccess($this->_logoutUser());
                    break;
                default:
                    $this->returnError();
            }
        } catch (Exception $e)
        {
            $this->returnError($e->getMessage());
        }
    }


    public function actionItem()
    {
        $request = Yii::app()->request;
        $id = $request->getParam('id', 0, AHttpRequest::PARAM_TYPE_NUMERIC);
        switch ($request->method)
        {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess($this->_renderOrder());
                break;
            case AHttpRequest::METHOD_POST:

                $this->returnSuccess($this->_renderOrder());
                break;

            default:
                $this->returnError();
        }
    }

    public function actionRoster()
    {
        $request = Yii::app()->request;
        $user = $request->getRequiredRawBodyParam('user', [], AHttpRequest::PARAM_TYPE_ARRAY);
        switch ($request->method)
        {
            case AHttpRequest::METHOD_POST:
                $this->returnSuccess($this->_rosterUser($user));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionRecover()
    {
        $request = Yii::app()->request;
        $email = $request->getRequiredRawBodyParam('email', [], AHttpRequest::PARAM_TYPE_STRING);
        switch ($request->method)
        {
            case AHttpRequest::METHOD_POST:
                $this->returnSuccess($this->_recoverUser($email));
                break;
            default:
                $this->returnError();
        }
    }

    private function _renderOrder()
    {
        try
        {
            $data = ['pilots' => [], 'ranks' => [], 'instructors' => [], 'awards' => []];
            $users = User::model()->findAll(['order' => 'nickname desc']);
            if (!$users)
                throw new Exception("Some error?");
            foreach ($users as $user)
                $data['pilots'][$user->id] = $user->shortAttributes;

            $ranks = Rank::model()->scopeRanks()->scopeCorrectOrder()->findAll();
            if (!$ranks)
                throw new Exception("Some error?");

            foreach ($ranks as $rank)
                $data['ranks'][$rank->id] = $rank->shortAttributes;

            $ranks = Rank::model()->scopeInstructors()->findAll();
            if (!$ranks)
                throw new Exception("Some error?");

            foreach ($ranks as $rank)
                $data['instructors'][$rank->id] = $rank->shortAttributes;

            $awards = Award::model()->findAll();
            if (!$awards)
                throw new Exception("Some error?");

            foreach ($awards as $award)
                $data['awards'][$award->id] = $award->shortAttributes;


            return $data;
        } catch (Exception $e)
        {
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

    public function actionGetOrders()
    {
        /**
         *
         */
        $list = Yii::app()->ts->groupList();
        foreach ($list as $group)
        {
            /**
             * @var TeamSpeak3_Node_Servergroup $group
             */
            $info = $group->getInfo();
            $simpleInfo = [
                'id' => $info['sgid'],
                'name' => $info['name']->toString()
            ];
            $icon = $group->iconDownload();
            if ($icon && !file_exists(dirname(Yii::app()->basePath) . '/img/groups/' . $info['sgid'] . '.png'))
            {
                @file_put_contents(dirname(Yii::app()->basePath) . '/img/groups/' . $info['sgid'] . '.png', $icon->toString());
            }

        }

    }

}