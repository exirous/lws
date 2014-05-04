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
        }
        catch (Exception $e)
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
                if ($id)
                    $this->returnSuccess($this->_renderUser($id));
                else
                    $this->returnSuccess($this->_renderUserList());
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

    public function actionPromote()
    {
        $request = Yii::app()->request;
        $userId = $request->getRequiredRawBodyParam('userId', 0);
        $courseId = $request->getRequiredRawBodyParam('courseId', 0);
        $this->returnSuccess($this->_promote($userId, $courseId));
    }

    private function _acceptRostered($id, $tsId)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            $user = User::model()->findByPk($id);
            if (!$user)
                throw new Exception('Пользователь не найден');

            $user->accept($tsId);
            $transaction->commit();
        }
        catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _renderUser($id)
    {
        try
        {
            $user = User::model()->findByPk($id);
            if (!$user)
                throw new Exception("User not found!");
            return $user->publicAttributes;
        }
        catch (Exception $e)
        {
            $this->returnError($e->getMessage());
        }
    }

    private function _renderUserList()
    {
        try
        {
            $usersOut = [];
            $users = User::model()->findAll(['condition'=>'rank_id>0','order' => 'nickname desc']);
            if (!$users)
                throw new Exception("Some error?");

            foreach ($users as $user)
                $usersOut[] = $user->shortAttributes;
            return $usersOut;
        }
        catch (Exception $e)
        {
            $this->returnError($e->getMessage());
        }
    }

    private function _loginUser($username, $password)
    {
        $userModel = null;
        try
        {
            $identity = new UserIdentity($username, $password);
            if ($identity->authenticate())
                Yii::app()->user->login($identity, 3600 * 24 * 365);
            else
                throw new Exception("Не правильный логин или пароль!");

            return $identity->_model->privateAttributes;
        }
        catch (Exception $e)
        {
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
        try
        {
            $userModel = User::roster($user);
            if (!$userModel)
                throw new Exception("Что-то пошло не так... Администратор оповещён");

            $identity = new UserIdentity($userModel->email, '');
            if ($identity->forceAuthenticate($userModel))
                Yii::app()->user->login($identity, 3600 * 24 * 365);
            else
                throw new Exception("Что-то пошло не так... Администратор оповещён");

            $transaction->commit();
        }
        catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return Yii::app()->user->privateAttributes;
    }

    private function _recoverUser($email)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            ////User::recover($email);
            $transaction->commit();
        }
        catch (Exception $e)
        {
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
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;
        $user = User::model()->findByPk($id)->getMarkAttributes();
        return $user;
    }

    private function _saveUserMark($userId, $subjectId, $mark)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;

        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            UserMark::saveMark($userId, $subjectId, $mark);
            $transaction->commit();
        }
        catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return [];
    }

    private function _promote($userId, $courseId)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->instructor_id)
            return null;

        $user = null;
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            $user = User::model()->findByPk($userId);
            if (!$user)
                throw new Exception('Cannot find user');
            $user->promoteCourse($courseId);
            $transaction->commit();
            $user->refresh();
        }
        catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }
        return $user->getShortMarkAttributes();
    }


    /*
        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            $string = null;
            $value = isset($translation['value']) ? $translation['value'] : '';
            switch ($type)
            {
                case 'string':
                    $string = StringTranslation::saveTranslation($stringId, $languageId, $value);
                    break;
                case 'plural':
                    //Here we use plural_string_id
                    $string = PluralStringTranslation::saveTranslation($stringId, $languageId, $value);
                    break;
                default:
                    throw  new Exception('Failed to save string translation. Reason: Input Data Error');
            }

            $transaction->commit();
            $this->returnSuccess(['string' => $string], Yii::t('app', 'String translation successfully updated'));
        }
        catch (Exception $e)
        {
            $transaction->rollback();
            $this->returnError($e->getMessage());
        }

     * */

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