<?php

class BattlelogController extends Controller
{
    public function actionList($page = 1, $perPage = 7)
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('userId', 0);
                $this->returnSuccess($this->_getList($id, $page, $perPage));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionGet()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('id', 0);
                $this->returnSuccess(BattleEvent::model()->findByPk(intval($id))->attributes);
                break;
            default:
                $this->returnError();
        }
    }

    public function actionDelete()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_POST:
                $id = $request->getRawBodyParam('id', 0);
                BattleEvent::model()->findByPk(intval($id))->delete();
                $this->returnSuccess(['result'=>'OK']);
                break;
            default:
                $this->returnError();
        }
    }

    public function actionAdd()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_POST:

                $userId = $request->getRawBodyParam('user_id', 0);
                $time = $request->getRawBodyParam('time', 0);
                $mission = $request->getRawBodyParam('mission', '');
                $result = $request->getRawBodyParam('result', '');
                $flightTime = $request->getRawBodyParam('flight_time', 0);
                $airTargets = $request->getRawBodyParam('air_targets', 0);
                $groundTargets = $request->getRawBodyParam('ground_targets', 0);
                $finePoints = $request->getRawBodyParam('fine_points', 0);
                $finePointsTimes = $request->getRawBodyParam('fine_points_times', 0);
                if (Yii::app()->user->isGuest || (!Yii::app()->user->model->isInstructor() && Yii::app()->user->id != 1))
                    return $this->returnError('Нет доступа!');
                $battleEvent = new BattleEvent();
                $battleEvent->user_id = $userId;
                $battleEvent->time = $time;
                $battleEvent->mission = $mission;
                $battleEvent->result = $result;
                $battleEvent->flight_time = $flightTime;
                $battleEvent->air_targets = $airTargets;
                $battleEvent->ground_targets = $groundTargets;
                $battleEvent->fine_points = $finePoints;
                $battleEvent->fine_points_times = $finePointsTimes;
                if (!$battleEvent->save())
                    return $this->returnError('Error!', $battleEvent->errors);
                $this->returnSuccess();
                break;
            default:
                $this->returnError();
        }
    }

    public function actionSave()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_POST:
                $id = $request->getRawBodyParam('id', 0);
                $time = $request->getRawBodyParam('time', 0);
                $mission = $request->getRawBodyParam('mission', '');
                $result = $request->getRawBodyParam('result', '');
                $flightTime = $request->getRawBodyParam('flight_time', 0);
                $airTargets = $request->getRawBodyParam('air_targets', 0);
                $groundTargets = $request->getRawBodyParam('ground_targets', 0);
                $finePoints = $request->getRawBodyParam('fine_points', 0);
                $finePointsTimes = $request->getRawBodyParam('fine_points_times', 0);
                if (Yii::app()->user->isGuest || (!Yii::app()->user->model->isInstructor() && Yii::app()->user->id != 1))
                    return $this->returnError('Нет доступа!');
                $battleEvent = BattleEvent::model()->findByPk(intval($id));
                if (!$battleEvent)
                    return $this->returnError('Не найден!');
                $battleEvent->time = $time;
                $battleEvent->mission = $mission;
                $battleEvent->result = $result;
                $battleEvent->flight_time = $flightTime;
                $battleEvent->air_targets = $airTargets;
                $battleEvent->ground_targets = $groundTargets;
                $battleEvent->fine_points = $finePoints;
                $battleEvent->fine_points_times = $finePointsTimes;
                if (!$battleEvent->save())
                    return $this->returnError('Error!', $battleEvent->errors);
                $this->returnSuccess();
                break;
            default:
                $this->returnError();
        }
    }

    private function _getList($userId, $page, $perPage)
    {
        return BattleEvent::getForUser($userId, $page, $perPage);
    }
}