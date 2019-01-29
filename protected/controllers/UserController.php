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

    public function actionLogin()
    {
        $request = Yii::app()->request;
        $email = $request->getRequiredRawBodyParam('email', null, AHttpRequest::PARAM_TYPE_STRING);
        $password = $request->getRequiredRawBodyParam('password', null, AHttpRequest::PARAM_TYPE_STRING);
        switch ($request->method) {
            case AHttpRequest::METHOD_POST:
                $this->returnSuccess($this->_loginUser($email, $password));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionLogout()
    {
        try {
            $request = Yii::app()->request;
            switch ($request->method) {
                case AHttpRequest::METHOD_POST:
                    $this->returnSuccess($this->_logoutUser());
                    break;
                default:
                    $this->returnError();
            }
        } catch (Exception $e) {
            $this->returnError($e->getMessage());
        }
    }


    public function actionItem()
    {
        $request = Yii::app()->request;
        $id = $request->getParam('id', 0, AHttpRequest::PARAM_TYPE_NUMERIC);
        $noMedals = $request->getParam('noMedals', false);
        switch ($request->method) {
            case AHttpRequest::METHOD_GET:
                if ($id)
                    $this->returnSuccess($this->_renderUser($id, $noMedals));
                else {
                    $filters = $request->getParam('filters', '', AHttpRequest::PARAM_TYPE_STRING);
                    $filters = @json_decode($filters, true);
                    $this->returnSuccess($this->_renderUserList($filters));
                }
                break;
            default:
                $this->returnError();
        }
    }

    public function actionPersonalFile()
    {
        $request = Yii::app()->request;
        switch ($request->method)
        {
            case AHttpRequest::METHOD_POST:
                $text = $request->getRequiredRawBodyParam('text', '', AHttpRequest::PARAM_TYPE_STRING);
                $id = $request->getRequiredRawBodyParam('id', 0, AHttpRequest::PARAM_TYPE_NUMERIC);
                $this->returnSuccess($this->_savePersonalFile($id, $text));
                break;
            case AHttpRequest::METHOD_GET:
                $id = $request->getRequiredParam('id', 0, AHttpRequest::PARAM_TYPE_NUMERIC);
                $forEdit = !!intval($request->getParam('forEdit', 0, AHttpRequest::PARAM_TYPE_NUMERIC));
                $this->returnSuccess($this->_getPersonalFile($id, $forEdit));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionGetIdFromUid()
    {
        $request = Yii::app()->request;
        $uid = $request->getParam('uid', 'NOUID');
        $user = User::model()->findByAttributes(['ts_id' => $uid]);
        if (!$user)
            $this->returnSuccess([]);
        else
            $this->returnSuccess(['id' => $user->id]);
    }

    public function actionVacation()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_GET:
                $id = $request->getParam('id', 0, AHttpRequest::PARAM_TYPE_NUMERIC);
                if ($id)
                    $this->returnSuccess($this->_renderUser($id));
                else {
                    $filters = $request->getParam('filters', '', AHttpRequest::PARAM_TYPE_STRING);
                    $filters = @json_decode($filters, true);
                    $this->returnSuccess($this->_renderUserList($filters));
                }
                break;

            case AHttpRequest::METHOD_POST:
                $dateFrom = $request->getRequiredRawBodyParam('dateFrom', '');
                $dateTo = $request->getRequiredRawBodyParam('dateTo', '');
                $reason = $request->getRequiredRawBodyParam('reason', '');
                $this->returnSuccess($this->_saveVacation($dateFrom, $dateTo, $reason));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionAcquit()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_POST:
                $pilotId = $request->getRequiredRawBodyParam('pilotId', '');
                $dateFrom = $request->getRequiredRawBodyParam('dateFrom', '');
                $dateTo = $request->getRequiredRawBodyParam('dateTo', '');
                $reason = $request->getRequiredRawBodyParam('reason', '');
                $this->returnSuccess($this->_saveAcquit($pilotId, $dateFrom, $dateTo, $reason));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionBirthdays()
    {

        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess($this->_renderBirthdayList());
                break;
            default:
                $this->returnError();
        }
    }

    public function actionRoster()
    {
        $request = Yii::app()->request;
        $user = $request->getRequiredRawBodyParam('user', [], AHttpRequest::PARAM_TYPE_ARRAY);
        $user['ip'] = $request->getUserHostAddress();
        switch ($request->method) {
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
        switch ($request->method) {
            case AHttpRequest::METHOD_POST:
                $this->returnSuccess($this->_recoverUser($email));
                break;
            default:
                $this->returnError();
        }
    }


    public function actionUpdate()
    {
        $request = Yii::app()->request;
        $user = $request->getRequiredRawBodyParam('user', [], AHttpRequest::PARAM_TYPE_ARRAY);
        switch ($request->method) {
            case AHttpRequest::METHOD_POST:
                $this->returnSuccess($this->_updateUser($user));
                break;
            default:
                $this->returnError();
        }
    }

    public function actionGetRoster()
    {
        $request = Yii::app()->request;
        $id = $request->getParam('userId', 0);
        if ($id)
            $this->returnSuccess($this->_getRosteredUser($id));
        else
            $this->returnSuccess($this->_getRosteredUsers());
    }

    public function actionGetMarks()
    {
        $request = Yii::app()->request;
        $id = $request->getRequiredParam('userId', 0);
        $this->returnSuccess($this->_getUserMarks($id));
    }

    public function actionUnreadMessages()
    {
        $request = Yii::app()->request;
        $ts_id = $request->getParam('ts_id', "A");
        $user = User::model()->find(['condition' => 'ts_id=:tsId', 'params' => [':tsId' => $ts_id]]);
        if (!$user)
            $this->returnSuccess();
        else {
            $messages = [];
            $unreadMessages = PrivateMessage::model()->findAllByAttributes(['reciever_id' => $user->id, 'is_read' => '0']);
            foreach ($unreadMessages as $message)
                $messages[] = $message->getRenderAttributes();
            $user->updateOnlineTime();
            $this->returnSuccess($messages);
        }
    }

    public function actionSaveMark()
    {
        $request = Yii::app()->request;
        $userId = $request->getRequiredRawBodyParam('userId', 0);
        $subjectId = $request->getRequiredRawBodyParam('subjectId', 0);
        $mark = $request->getRequiredRawBodyParam('mark', 0);
        $this->returnSuccess($this->_saveUserMark($userId, $subjectId, $mark));
    }

    public function actionAccept()
    {
        $request = Yii::app()->request;
        $id = $request->getRequiredRawBodyParam('userId', 0);
        $uId = $request->getRequiredRawBodyParam('uid', '');
        $this->returnSuccess($this->_acceptRostered($id, $uId));
    }

    public function actionInactiveCount()
    {
        $this->returnSuccess(['count' => User::model()->scopeDefectors()->scopeEnabled()->count()]);
    }

    public function actionInactive()
    {
        $request = Yii::app()->request;
        switch ($request->method) {
            case AHttpRequest::METHOD_GET:
                $this->returnSuccess($this->_renderInactiveUserList());
                break;
            default:
                $this->returnError();
        }
    }


    public function actionExpel()
    {
        $request = Yii::app()->request;
        $id = $request->getRequiredRawBodyParam('userId', 0);
        $reason = $request->getRequiredRawBodyParam('reason', '');
        $this->returnSuccess($this->_expelInactive($id, $reason));
    }

    public function actionReenlist()
    {
        $request = Yii::app()->request;
        $id = $request->getRequiredRawBodyParam('userId', 0);
        $reason = $request->getRawBodyParam('reason', '');
        $this->returnSuccess($this->_reenlist($id, $reason));
    }

    public function actionReject()
    {
        $request = Yii::app()->request;
        $id = $request->getRequiredRawBodyParam('userId', 0);
        $reason = $request->getRequiredRawBodyParam('reason', '');
        $this->returnSuccess($this->_rejectRostered($id, $reason));
    }


    public function actionPromote()
    {
        $request = Yii::app()->request;
        $userId = $request->getRequiredRawBodyParam('userId', 0);
        $courseId = $request->getRequiredRawBodyParam('courseId', 0);
        $promoteToOfficer = $request->getRawBodyParam('promoteToOfficer', false);
        $this->returnSuccess($this->_promote($userId, $courseId, $promoteToOfficer));
    }

    public function actionSaveEvent()
    {
        $request = Yii::app()->request;
        if ($request->method != AHttpRequest::METHOD_POST)
            $this->returnError();
        else {
            $eventId = $request->getRequiredRawBodyParam('id', 0);
            $date = $request->getRequiredRawBodyParam('dateString', '');
            $text = $request->getRequiredRawBodyParam('text', '');
            $userId = $request->getRequiredRawBodyParam('userId', 0);
            $this->returnSuccess($this->_updateEvent($eventId, $date, $text, $userId));
        }
    }

    public function actionSaveMedalPosition()
    {
        $request = Yii::app()->request;
        if ($request->method != AHttpRequest::METHOD_POST)
            $this->returnError();
        else {
            $awardId = $request->getRequiredRawBodyParam('id', 0);
            $userId = $request->getRequiredRawBodyParam('userId', 0);

            $top = $request->getRequiredRawBodyParam('top', '');
            $left = $request->getRequiredRawBodyParam('left', '');
            $this->returnSuccess($this->_updateMedalPosition($awardId, $userId, $top, $left));
        }
    }

    public function actionDeleteEvent()
    {
        $request = Yii::app()->request;
        if ($request->method != AHttpRequest::METHOD_POST)
            $this->returnError();
        else {
            $eventId = $request->getRequiredRawBodyParam('eventId', 0);
            $this->returnSuccess($this->_deleteEvent($eventId));
        }
    }

    public function actionSync()
    {
        $request = Yii::app()->request;
        if ($request->method != AHttpRequest::METHOD_POST)
            $this->returnError();
        else {
            $id = $request->getRequiredRawBodyParam('id', 0);
            $User = User::model()->findByPk($id);
            $User->syncWithTeamSpeak();
            $this->returnSuccess(['result' => 'OK']);
        }
    }

    public function actionCheckRecoveryToken()
    {
        $request = Yii::app()->request;
        $token = trim($request->getRequiredParam('token', ''));
        try {
            if (User::isRecoveryTokenOK($token))
                $this->returnSuccess(['result' => 'OK']);
            else
                throw new Exception('Token Invalid');
        } catch (Exception $e) {
            $this->returnError($e->getMessage());
        }
    }

    public function actionRecoverPassword()
    {
        $request = Yii::app()->request;
        $token = trim($request->getRequiredRawBodyParam('token', ''));
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $password = $request->getRequiredRawBodyParam('password', '');
            $userModel = User::resetPassword($token, $password);
            if (!$userModel)
                throw new Exception("Что-то пошло не так... Администратор оповещён");
            $identity = new UserIdentity($userModel->email, '');
            if ($identity->forceAuthenticate($userModel))
                Yii::app()->user->login($identity, 3600 * 24 * 365);
            else
                throw new Exception("Что-то пошло не так... Администратор оповещён");
            $transaction->commit();
            $this->returnSuccess(Yii::app()->user->privateAttributes);
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
    }

    public function actionClearUpdate()
    {
        $request = Yii::app()->request;
        Update::model()->deleteAll('user_id=:user_id AND section=:section', ['user_id' => Yii::app()->user->id, 'section' => $request->getRequiredRawBodyParam('section')]);
        $this->returnSuccess(['result' => 'success']);
    }


    private function _updateEvent($id, $date, $text, $userId)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            if ($id > 0) {
                $event = UserEvent::model()->findByPk($id);
                if (!$event)
                    throw new Exception('event not found');
            } else {
                $event = new UserEvent();
                $event->user_id = $userId;
            }
            $event->date = $date;
            $event->text = $text;
            if (!$event->save())
                throw new Exception($event->getErrorsString());
            $transaction->commit();
            return $event->getPublicAttributes();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return null;
    }

    private function _updateUser($userData)
    {
        if (Yii::app()->user->isGuest || (!Yii::app()->user->model->instructor_id && Yii::app()->user->model->id != 1))
            return null;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            if (!$userData['id'] ||
                !$userData['nickname'] ||
                !$userData['firstname'] ||
                !$userData['ts_id'] ||
                !$userData['birthDate'] ||
                (!$userData['qualifications'] || (!$userData['qualifications']['fighter'] && !$userData['qualifications']['bomber'] && !$userData['qualifications']['shturmovik']))
            )
                throw new Exception('Чего-то не хватает!');

            $user = User::model()->findByPk($userData['id']);
            if (!$user)
                throw new Exception('Не найден!!');

            $user->firstname = $userData['firstname'];
            $user->nickname = $userData['nickname'];
            $user->ts_id = $userData['ts_id'];
            $user->is_clanner = $userData['is_clanner'];
            $user->birth_date = $userData['birthDate'];

            $qualifications = [];
            if (array_key_exists('fighter', $userData['qualifications']) && $userData['qualifications']['fighter'])
                $qualifications[] = 'fighter';
            if (array_key_exists('bomber', $userData['qualifications']) && $userData['qualifications']['bomber'])
                $qualifications[] = 'bomber';
            if (array_key_exists('shturmovik', $userData['qualifications']) && $userData['qualifications']['shturmovik'])
                $qualifications[] = 'shturmovik';

            $user->qualifications = implode(',', $qualifications);

            if (!$user->validate())
                throw new Exception($user->getErrorsString());
            if (!$user->save())
                throw new Exception('Ошибка!');

            $user->syncWithTeamSpeak();

            $transaction->commit();
            return $user->getEditAttributes();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return null;
    }

    private function _deleteEvent($id)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $event = UserEvent::model()->findByPk($id);
            if (!$event)
                throw new Exception('event not found');
            $event->delete();
            $transaction->commit();
            return [];
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return null;
    }

    public function actionUpload()
    {
        try {
            if (Yii::app()->user->isGuest)
                throw new Exception("ЭЭ??");

            $userId = Yii::app()->request->getRequiredParam('userId', 0);

            $user = User::model()->findByPk($userId); //Yii::app()->user->model;
            if ((Yii::app()->user->model->id != '14') && (Yii::app()->user->model->id != '1'))
                throw new Exception('Permission denied');

            if (!$user)
                throw new Exception('Пользовватель не найден');
            $src = substr(md5(time()), 0, 10);
            $file = CUploadedFile::getInstanceByName('file');
            $newFileName = $user->id . '_' . $src . '.jpg';
            $oldFileName = $user->id . '_' . $user->img_src . '.jpg';
            $user->img_src = $src;
            $image = Yii::app()->image->load($file->tempName);
            $image->resize(200, 200);
            $image->crop(200, 200);
            $image->resize(200, 200);
            $image->save(dirname(Yii::app()->basePath) . '/img/users/' . $newFileName); // or $image->save('images/small.jpg');
            if (file_exists(dirname(Yii::app()->basePath) . '/img/users/' . $oldFileName))
                unlink(dirname(Yii::app()->basePath) . '/img/users/' . $oldFileName);
            $user->save();
            $this->returnSuccess($src);
        } catch (Exception $e) {
            $this->returnError($e->getMessage());
        }
    }

    private function _acceptRostered($id, $tsId)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = User::model()->findByPk($id);
            if (!$user)
                throw new Exception('Пользователь не найден');

            $user->accept($tsId);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _rejectRostered($id, $reason)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = User::model()->findByPk($id);
            if (!$user)
                throw new Exception('Пользователь не найден');
            $user->reject($reason);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _expelInactive($id, $reason)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = User::model()->findByPk($id);
            if (!$user)
                throw new Exception('Пользователь не найден');
            $user->expel($reason);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _renderUser($id, $noMedals = false)
    {
        try {
            $user = User::model()->findByPk($id);
            if (!$user)
                throw new Exception("User not found!");
            if ($noMedals)
                return $user->getEditAttributes();
            else
                return $user->getPublicAttributes();
        } catch (Exception $e) {
            $this->returnError($e->getMessage());
        }
    }

    private function _renderUserList($filters)
    {
        try {
            $users = User::model()->with('rank');

            if (isset($filters['name']) && $filters['name'])
                $users = $users->scopeName($filters['name']);

            if ($filters['which'] == '3') {
                if (!in_array(Yii::app()->user->model->id, [1, 14])) {
                    $filters['which'] = '0';
                }
            }
            if ($filters['which'] == '2') {
                if (!Yii::app()->user->model->canMakeOrders()) {
                    $filters['which'] = '0';
                }
            }
            switch ($filters['which'])
            {
                case '3':
                    $users = $users->scopeDisabled();
                    break;
                case '2':
                    $users = $users->scopeEnabled()->scopeDefectors();
                    break;
                case '1':
                    $users = $users->scopeEnabled()->scopeVacation();
                    break;
                default:
                    $users = $users->scopeEnabled()->scopeActive();
            }


            $usersOut = [];
            $users = $users->scopeWithRank()->with('activeVacation')->findAll(['condition' => 'rank_id>0 AND rank_id<>8', 'order' => 'rank.order desc, nickname']);

            foreach ($users as $user) {
                $usersOut[] = $user->listAttributes;
            }

            return $usersOut;
        } catch (Exception $e) {
            $this->returnError($e->getMessage());
        }
        return null;
    }

    private function _renderBirthdayList()
    {
        try {
            $users = User::model()->with('rank');
            $usersOut = [];
            $users = $users->scopeWithRank()->scopeEnabled()->scopeClosestBirthdays()->findAll();
            foreach ($users as $user) {
                $attributes = $user->listAttributes;
                $date = new DateTime($user->birth_date);
                $date = $date->setDate(date("Y"), $date->format('m'), $date->format('d'));
                $attributes['birthday'] = $date->getTimestamp() . '000';
                if ($date->format('m-d') == date("m-d"))
                    $attributes['today'] = true;
                if ($date->format('m-d') == date("m-d", time() + 24 * 3600))
                    $attributes['tomorow'] = true;
                $usersOut[] = $attributes;
            }
            usort($usersOut, function ($a, $b) {
                return ($a['birthday'] - $b['birthday']);
            });
            return $usersOut;
        } catch (Exception $e) {
            $this->returnError($e->getMessage());
        }
        return null;
    }

    private function _loginUser($username, $password)
    {
        $userModel = null;
        try {
            $identity = new UserIdentity($username, $password);
            if ($identity->authenticate())
                Yii::app()->user->login($identity, 3600 * 24 * 365);
            else
                throw new Exception("Не правильный логин или пароль!");

            return $identity->_model->privateAttributes;
        } catch (Exception $e) {
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _logoutUser()
    {
        Yii::app()->user->logout();
        return [];
    }

    private function _rosterUser($user)
    {
        $transaction = Yii::app()->db->beginTransaction();
        $userModel = null;
        try {
            $userModel = User::roster($user);
            if (!$userModel)
                throw new Exception("Что-то пошло не так... Администратор оповещён");

            $identity = new UserIdentity($userModel->email, '');
            if ($identity->forceAuthenticate($userModel))
                Yii::app()->user->login($identity, 3600 * 24 * 365);
            else
                throw new Exception("Что-то пошло не так... Администратор оповещён");

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return Yii::app()->user->privateAttributes;
    }

    private function _recoverUser($email)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            User::recover($email);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return []; //Yii::app()->user->privateAttributes;
    }

    private function _getRosteredUsers()
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;

        $rosterArray = [];
        foreach (User::model()->scopeJustRostered()->findAll() as $user)
            $rosterArray[] = $user->getRosterAttributes();

        return $rosterArray;
    }

    private function _getRosteredUser($id)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;
        $user = User::model()->findByPk($id)->getRosterAttributes();
        $user['possibleUsers'] = Yii::app()->ts->findUsersLike($user['nickname'], $user['ip']);
        return $user;
    }

    private function _getUserMarks($id)
    {
        if (Yii::app()->user->isGuest || (!Yii::app()->user->model->instructor_id && ($id != Yii::app()->user->model->id)))
            return null;
        $user = User::model()->findByPk($id)->getMarkAttributes();
        return $user;
    }

    private function _getUserVacations($id)
    {
        if (Yii::app()->user->isGuest || (!Yii::app()->user->model->instructor_id && ($id != Yii::app()->user->model->id)))
            return null;
        $user = User::model()->findByPk($id)->getVacationAttributes();
        return $user;
    }

    private function _saveUserMark($userId, $subjectId, $mark)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;

        $transaction = Yii::app()->db->beginTransaction();
        try {
            UserMark::saveMark($userId, $subjectId, $mark);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _saveVacation($dateFrom, $dateTo, $reason)
    {
        if (Yii::app()->user->isGuest)
            return null;

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $userId = Yii::app()->user->model->id;
            $userNickname = Yii::app()->user->model->nickname;
            $vacation = new Vacation();
            $vacation->reason = $reason;
            $vacation->date_from = $dateFrom;
            $vacation->date_to = $dateTo;
            $vacation->user_id = $userId;
            if (!$vacation->save())
                throw new Exception("Ошибка сохранения");
            $transaction->commit();
            Mailer::send('luftwaffeschule@gmail.com', 'Рапорт на отпуск от: ' . $userNickname,
                Yii::app()->controller->renderPartial('//mails/vacation_report',
                    compact('reason', 'dateFrom', 'dateTo', 'userId', 'userNickname'), true));
            return $vacation->getPublicAttributes();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _saveAcquit($userId, $dateFrom, $dateTo, $reason)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->canMakeOrders())
            return null;

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = User::model()->findByPk($userId);
            if (!$user)
                throw new Exception("Такой пользователь не существует");
            $userNickname = $user->nickname;
            $vacation = new Vacation();
            $vacation->reason = $reason;
            $vacation->date_from = $dateFrom;
            $vacation->date_to = $dateTo;
            $vacation->user_id = $userId;
            if (!$vacation->save())
                throw new Exception("Ошибка сохранения");
            $user->is_defector = 0;
            $user->save();
            $transaction->commit();
            Mailer::send('luftwaffeschule@gmail.com', 'Принудительный рапорт на отпуск для: ' . $userNickname,
                Yii::app()->controller->renderPartial('//mails/vacation_report',
                    compact('reason', 'dateFrom', 'dateTo', 'userId', 'userNickname'), true));
            return $vacation->getPublicAttributes();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _promote($userId, $courseId, $promoteToOfficer)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;

        $user = null;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = User::model()->findByPk($userId);
            if (!$user)
                throw new Exception('Cannot find user');

            $user->promoteCourse($courseId, $promoteToOfficer);
            $transaction->commit();
            $user->refresh();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return $user->getShortMarkAttributes();
    }


    private function _updateMedalPosition($awardId, $userId, $top, $left)
    {
        if (Yii::app()->user->isGuest || (Yii::app()->user->id != '1' && Yii::app()->user->id != '14'))
            return null;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $userAward = UserAward::model()->find(['condition' => 'user_id=:userId AND award_id=:awardId', 'params' => ['userId' => $userId, 'awardId' => $awardId]]);
            if (!$userAward)
                throw new Exception('Cannot find Award');

            if ($top > 430)
                $userAward->delete();
            else {
                $userAward->top = $top;
                $userAward->left = $left;
                $userAward->save();
            }
            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return 'OK';
    }

    private function _renderInactiveUserList()
    {
        try {
            $users = User::model()->with('rank');

            if (isset($filters['name']) && $filters['name'])
                $users = $users->scopeName($filters['name']);

            $usersOut = [];
            $users = $users->scopeDefectors()->scopeEnabled()->scopeWithRank()->findAll(['order' => 'last_online_time, rank.order, nickname']);

            foreach ($users as $user)
                $usersOut[] = $user->inactiveAttributes;

            return $usersOut;
        } catch (Exception $e) {
            $this->returnError($e->getMessage());
        }
        return null;
    }


    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    public function actionTest()
    {
        $users = User::model()->findAll();
        foreach ($users as $user) {
              if ($user->instructor_id)
              {
                  $instructor_map = [
                      '9' => '76', '27' => '75', '28' => '74'
                  ];
                  $award = UserAward::model()->findByAttributes(['user_id' => $user->id, 'award_id' => $instructor_map[$user->instructor_id]]);
                  if (!$award)
                  {
                      $award = new UserAward();
                      $award->attributes = ['user_id' => $user->id, 'award_id' => $instructor_map[$user->instructor_id]];
                      $award->save();
                      var_dump($award->getErrorsString());
                  }

              }
            }

        die(var_dump("!!"));

    }

    public function actionWakePc()
    {
        die(shell_exec('wakeonlan 74:D4:35:85:75:A5'));
    }

    private function _reenlist($id, $reason)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = User::model()->findByPk($id);
            if (!$user)
                throw new Exception('Пользователь не найден');
            $user->reenlist($reason);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _savePersonalFile($id, $text)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->isInstructor())
            return null;
        $user = User::model()->findByPk($id);
        if (!$user->personal_file_id)
            $file = new Text();
        else
            $file = $user->personalFile;
        $file->title = 'PersonalFile';
        $file->text = $text;
        $file->save();
        if (!$user->personal_file_id)
            $user->personal_file_id = $file->id;
        $user->save();
        return [
            'id'    => $user->id,
            'text'  => $text,
            'title' => 'Личное дело ' . $user->nickname
        ];
    }

    private function _getPersonalFile($id, $forEdit = false)
    {
        if (Yii::app()->user->isGuest || !(Yii::app()->user->model->isInstructor() || ($id == Yii::app()->user->model->id)))
            return null;
        $user = User::model()->findByPk($id);
        $text = '';
        if ($user->personalFile)
        {
            $text = $user->personalFile->text;
            if (!$forEdit)
            {
                require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
                $parser = new JBBCode\Parser();
                $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
                $parser->parse($text);
                $text = nl2br($parser->getAsHTML());
            }
        }
        return [
            'id'    => $user->id,
            'text'  => $text,
            'title' => 'Личное дело ' . $user->nickname
        ];
    }
}