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
 * @property Array shortAttributes
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

    public function rules()
    {
        $defaults = [
            ['join_date',
                'default',
                'value' => gmdate("Y-m-d H:i:s")
            ]
        ];
        return CMap::mergeArray($defaults, parent::rules());
    }

    public function scopeJustRostered()
    {
        $this->dbCriteria->mergeWith([
            'condition' => 'rank_id is NULL OR rank_id=8'
        ]);
        return $this;
    }

    public function  scopeWithRank()
    {
        $this->dbCriteria->mergeWith([
            'condition' => 'rank_id is NOT NULL'
        ]);
        return $this;
    }

    public function getPublicAttributes()
    {
        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'id' => $this->id
        ];
    }

    public function getRosterAttributes()
    {
        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'id' => $this->id,
            'roster' => json_decode($this->roster),
            'ip' => $this->ip
        ];
    }

    public function getShortAttributes()
    {
        $awards = [];
        foreach ($this->awards as $award)
            $awards[] = $award->id;

        return [
            'nickname' => $this->nickname,
            'id' => $this->id,
            'rank' => $this->rank_id,
            'old_rank' => $this->rank_id,
            'old_instructor' => $this->instructor_id,
            'rank_name' => $this->rank->name,
            'instructor' => $this->instructor_id,
            'awards' => $awards
        ];
    }

    public function getPrivateAttributes()
    {
        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'canMakeOrders' => ($this->rank_id && $this->rank->order > 6),
            'canMakeNews' => ($this->rank_id && $this->rank->order > 6),
            'isInstructor' => $this->instructor_id ? true : false,
            'fullname' => $this->nickname . ' (' . $this->firstname . ')',
            'id' => $this->id
        ];
    }

    public static function roster($user)
    {
        if (User::model()->exists(['condition' => 'email=:email', 'params' => [':email' => $user['private']['email']]]))
            throw new Exception('Пользователь с таким э-мэйлом уже существует!');
        if (!$user['private']['password'] || strlen($user['private']['password']) < 3)
            throw new Exception('Пароль должен быть длинее 3-х символов');
        $newUser = new User();
        $newUser->email = $user['private']['email'];
        $newUser->birth_date = $user['birthdate'];
        $newUser->password = md5($user['private']['password']);
        unset($user['private']);
        $newUser->nickname = $user['nickname'];
        $newUser->firstname = $user['firstname'];
        $newUser->ip = $user['ip'];
        $newUser->roster = json_encode($user);
        if (!$newUser->validate())
            throw new Exception($newUser->getErrors());
        if (!$newUser->save())
            throw new Exception('Возникла непредвиденная ошибка, мы над этим работаем...');
        return $newUser;
    }

    public function syncWithTeamSpeak()
    {
        $dbId = Yii::app()->ts->ts3Server->clientFindDb(Yii::app()->user->model->ts_id, true);
        if (count($dbId))
        {
            $dbId = $dbId[0];
            $groups = Yii::app()->ts->ts3Server->clientGetServerGroupsByDbid($dbId);
            $ignoreRank = false;
            $ignoreInstructor = false;
            foreach ($groups as $groupId => $dummy)
            {
                if ($groupId == 8 || $groupId == 6)
                    continue;
                if ($groupId == $this->rank_id)
                {
                    $ignoreRank = true;
                    continue;
                }
                if ($groupId == $this->instructor_id)
                {
                    $ignoreInstructor = true;
                    continue;
                }

                Yii::app()->ts->ts3Server->serverGroupClientDel($groupId, $dbId);
            }
            if (!$ignoreRank)
                Yii::app()->ts->ts3Server->serverGroupClientAdd($this->rank_id, $dbId);

            if (!$ignoreInstructor && $this->instructor_id)
                Yii::app()->ts->ts3Server->serverGroupClientAdd($this->instructor_id, $dbId);
        }
        else
        {
            throw new Exception('Пользователь не прикреплён к TeamSpeak');
        }
    }
}