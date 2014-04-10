<?php

Yii::import('application.models._base.BaseUser');

/**
 * @method User find
 * @method User[] findAll
 * @method User findByPk
 * @method User[] findAllByPk
 * @method User findByAttributes
 * @method User[] findAllByAttributes
 * @method User findBySql
 * @method User[] findAllBySql
 * @method User cache
 * @method User resetScope
 * @method User with
 * @method User together
 * @method User populateRecord
 * @method User scopeLimit
 * @method User scopeOffset
 * @method User scopeOrder
 * @method User scopeAllColumns
 * @method User scopeSelect
 * @method User byName
 * @property Array publicAttributes
 * @property Array privateAttributes
 */
class User extends BaseUser
{
    /**
     * @static
     * @param string $className
     * @return User
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getPublicAttributes()
    {
        return [
            'nickname' => $this->nickname,
            'firstname' => $this->fistname,
            'id' => $this->id
        ];
    }

    public function getPrivateAttributes()
    {
        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'fullname' => $this->nickname . ' (' . $this->firstname . ')',
            'id' => $this->id
        ];
    }

    public static function roster($user)
    {
        //die(var_dump($user));
        if (User::model()->exists(['condition' => 'email=:email', 'params' => [':email' => $user['private']['email']]]))
            throw new Exception('Пользователь с таким э-мэйлом уже существует!');
        if (!$user['private']['password'] || strlen($user['private']['password']) < 3)
            throw new Exception('Пароль должен быть длинее 3-х символов');
        $newUser = new User();
        $newUser->email = $user['private']['email'];
        $newUser->password = md5($user['private']['password']);
        unset($user['private']);
        $newUser->nickname = $user['nickname'];
        $newUser->firstname = $user['firstname'];
        $newUser->roster = json_encode($user);
        if (!$newUser->validate())
            throw new Exception($newUser->getErrors());
        if (!$newUser->save())
            throw new Exception('Возникла непредвиденная ошибка, мы над этим работаем...');
        return $newUser;
    }

}