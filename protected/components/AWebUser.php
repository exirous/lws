<?php

/**
 * Created by PhpStorm.
 * User: ExiRouS
 * Date: 4/5/2014
 * Time: 13:49
 * @property array privateAttributes
 * @property User model
 */
class AWebUser extends CWebUser
{
    private $_model;

    public function getPrivateAttributes()
    {
        if ($this->isGuest)
            return [
                'fullname' => 'Неизвестный Гость',
                'isGuest' => true
            ];
        else
            return CMap::mergeArray([
                'isGuest' => false,
            ], $this->getModel()->privateAttributes);
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->_model)
            $this->_model = User::model()->resetScope()->findByPk($this->getId());
        if (!$this->_model) {
            $this->logout();
            return null;
        }
        return $this->_model;
    }

    public function canViewUserEmailAddress($id = false)
    {
        if (!$this->isGuest && (($id == $this->model->id) || ($this->model->rank->order > 11))) {
            return true;
        }
        return false;
    }

    /**
     * Logs in a user.
     *
     * The user identity information will be saved in storage that is
     * persistent during the user session. By default, the storage is simply
     * the session storage. If the duration parameter is greater than 0,
     * a cookie will be sent to prepare for cookie-based login in future.
     *
     * Note, you have to set {@link allowAutoLogin} to true
     * if you want to allow user to be authenticated based on the cookie information.
     *
     * @param UserIdentity $identity the user identity (which should already be authenticated)
     * @param integer $duration number of seconds that the user can remain in logged-in status. Defaults to 0, meaning login till the user closes the browser.
     * If greater than 0, cookie-based login will be used. In this case, {@link allowAutoLogin}
     * must be set true, otherwise an exception will be thrown.
     * @return boolean whether the user is logged in
     */
    public function login($identity, $duration = 0)
    {
        $response = parent::login($identity, $duration);
        if ($response)
            $this->_model = $identity->_model;
        else
            $this->_model = null;

        return $response;
    }

}