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
                $data = $request->getRequiredRawBodyParam('data');
                $this->returnSuccess($this->_makeOrder($data));
                break;
            default:
                $this->returnError();
        }
    }

    private function _makeOrder($data)
    {
        $orderId = null;
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->canMakeOrders())
            return null;

        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            $orderId = Order::issueOrder($data);
            $transaction->commit();
        }
        catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return $orderId;
    }

    private function _renderOrder()
    {
        try
        {
            $data = ['pilots' => [], 'ranks' => [], 'instructors' => [], 'awards' => []];
            $users = User::model()->scopeWithRank()->findAll(['order' => 'nickname desc']);
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