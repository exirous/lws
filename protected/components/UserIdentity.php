<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 * @property User _model
 */
class UserIdentity extends CUserIdentity
{
    private $_id;
    public $_model;

    public function authenticate()
    {
        $record = User::model()->resetScope()->findByAttributes(array('email' => $this->username));
        if ($record === null)
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        else if ($record->password !== md5($this->password))
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        else
        {
            $this->_id = $record->id;
            $this->_model = $record;
            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }




    /**
     * @param User $record
     * @return bool
     */
    public function forceAuthenticate($record)
    {
        if ($record === null)
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        else
        {
            $this->_id = $record->id;
            $this->_model = $record;
            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }

}