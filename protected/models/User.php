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
 * @property Vacation activeVacation
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

    /*public function defaultScope()
    {
        return [
            'condition' => 'is_disabled=0'
        ];
    }*/

    /**
     * @return User
     */
    public function scopeEnabled()
    {
        $this->dbCriteria->mergeWith([
            'condition' => 'is_disabled=0'
        ]);
        return $this;
    }


    public function relations()
    {
        $relations = [
            'activeVacation' => array(self::HAS_ONE, 'Vacation', 'user_id', 'on' => 'NOW() BETWEEN activeVacation.date_from And activeVacation.date_to')
        ];
        return CMap::mergeArray($relations, parent::relations());
    }

    public function scopeJustRostered()
    {
        $this->dbCriteria->mergeWith([
            'condition' => 'rank_id is NULL OR rank_id=8'
        ]);
        return $this;
    }

    public function scopeWithRank()
    {
        $this->dbCriteria->mergeWith([
            'condition' => 'rank_id is NOT NULL'
        ]);
        return $this;
    }

    /**
     * @param $name
     * @return User
     */
    public function scopeName($name)
    {
        $this->dbCriteria->mergeWith([
            'condition' => '`firstname` LIKE :name OR `nickname` LIKE :name',
            'params' => ['name' => '%' . $name . '%']
        ]);
        return $this;
    }

    public function scopeClosestBirthdays()
    {
        $this->dbCriteria->mergeWith([
            'condition' => 'DATE_ADD(birth_date, INTERVAL YEAR(CURDATE())-YEAR(birth_date) YEAR)
            BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY)'
        ]);
        return $this;
    }

    /**
     * @return User
     */
    public function scopeInactive()
    {
        $this->dbCriteria->mergeWith([
            'with' => ['vacations' => [
                'together'=>true
            ]],
            'condition' => '((t.`last_online_time` < NOW() - INTERVAL 1 MONTH) OR t.`last_online_time` IS NULL)
             AND t.`ts_id` IS NOT NULL
             AND (vacations.id IS NULL OR vacations.`date_to` < NOW() - INTERVAL 1 MONTH)'
        ]);
        return $this;
    }

    public function getPublicAttributes()
    {
        $awards = [];
        $events = [];
        /*if ($this->id == '1')
            foreach (Award::model()->findAll(['order' => '`order`']) as $award)
                $awards[$award->award_replace_id ? $award->award_replace_id : $award->id] = $award->shortAttributes;
        */
        foreach (UserAward::model()->with('award')->findAll(['condition' => 'user_id=:userId', 'params' => ['userId' => $this->id]]) as $userAward) {
            $award = $userAward->award;
            $attributes = $award->shortAttributes;
            if ($userAward->top) {
                $attributes['top'] = $userAward->top;
                $attributes['left'] = $userAward->left;
            }
            $awards[$award->award_replace_id ? $award->award_replace_id : $award->id] = $attributes;
        }
        $awards = array_values($awards);

        foreach ($this->userEvents as $event)
            $events[] = $event->publicAttributes;

        $qualifications = str_replace(['fighter', 'bomber', ','], ['Истребитель', 'Бомбардировщик', ', '], $this->qualifications);

        return [
            'nickname' => $this->nickname,
            'img_src' => $this->img_src,
            'firstname' => $this->firstname,
            'birthDate' => strtotime($this->birth_date) . '000',
            'joinDate' => strtotime($this->join_date) . '000',
            'id' => $this->id,
            'rank' => $this->rank_id ? $this->rank->getShortAttributes() : null,
            'instructor' => $this->instructor_id ? $this->instructor->getShortAttributes() : null,
            'is_clanner' => intval($this->is_clanner),
            'activeVacation' => $this->activeVacation,
            'isDisabled' => $this->is_disabled == '1' ? true : false,
            'lastOnline' => $this->last_online_time ? (strtotime($this->last_online_time) . '000') : (mktime(0,0,1,8,1,2014).'000'),
            'qualifications' => $qualifications,
            'medals' => $awards,
            'events' => $events

        ];
    }


    public function getEditAttributes()
    {
        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'birthDate' => strtotime($this->birth_date) . '000',
            'id' => $this->id,
            'is_clanner' => intval($this->is_clanner),
            'ts_id' => $this->ts_id,
            'qualifications' => ['fighter' => strpos($this->qualifications, 'fighter') !== false, 'bomber' => strpos($this->qualifications, 'bomber') !== false],
            'possibleUsers' => Yii::app()->ts->findUsersLike($this->nickname, $this->ip)
        ];
    }

    public function getRosterAttributes()
    {
        $roster = json_decode($this->roster, true);
        $roster['birthdate'] = strtotime($roster['birthdate']) . '000';

        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'id' => $this->id,
            'rank' => $this->rank_id,
            'roster' => $roster,
            'ip' => $this->ip
        ];
    }

    public function  getMarkAttributes()
    {
        $marks = [];
        foreach ($this->userMarks as $mark)
            $marks[$mark->subject->course_id][$mark->subject_id] = $mark->publicAttributes;

        $programs = [];
        foreach (Program::model()->findAll() as $program) {
            if (strpos($this->qualifications, $program->slug) !== false) {
                $programs[$program->slug]['id'] = $program->slug;
                foreach ($program->courses as $course)
                    $programs[$program->slug]['courses'][] = $course->publicAttributes;
            }
        }

        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'rank' => $this->rank_id,
            'is_clanner' => intval($this->is_clanner),
            'rank_order' => $this->rank->order,
            'marks' => $marks,
            'programs' => $programs
        ];
    }

    public function  getVacationAttributes()
    {
        $vacations = [];
        foreach ($this->vacations as $vacation)
            $vacations[] = $vacation->getPublicAttributes();
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'vacations' => $vacations,
        ];
    }

    public function  getShortMarkAttributes()
    {
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'rank' => $this->rank_id,
            'rank_order' => $this->rank->order,
        ];
    }

    public function getShortAttributes()
    {
        $awards = [];
        foreach ($this->awards as $award)
            $awards[] = $award->id;

        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'id' => $this->id,
            'rank' => $this->rank_id,
            'old_rank' => $this->rank_id,
            'old_instructor' => $this->instructor_id,
            'rank_name' => $this->rank->name,
            'instructor' => $this->instructor_id,
            'is_clanner' => intval($this->is_clanner),
            'awards' => $awards
        ];
    }

    public function getListAttributes()
    {
        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'birthday' => strtotime($this->birth_date) . '000',
            'id' => $this->id,
            'img_src' => $this->img_src,
            'rank' => $this->rank_id,
            'rank_name' => $this->rank->name,
            'instructor' => $this->instructor_id,
            'activeVacation' => $this->activeVacation,
            'isBomber' => strpos($this->qualifications, 'bomber') !== false,
            'is_clanner' => intval($this->is_clanner),
        ];
    }
    public function getInactiveAttributes()
    {
        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'id' => $this->id,
            'img_src' => $this->img_src,
            'rank' => $this->rank_id,
            'rank_name' => $this->rank->name,
            'instructor' => $this->instructor_id,
            'isDisabled' => $this->is_disabled == '1' ? true : false,
            'lastOnline' => $this->last_online_time ? (strtotime($this->last_online_time) . '000') : (mktime(0,0,1,8,1,2014).'000'),
            'lastWarning' => $this->last_warning_time ? (strtotime($this->last_warning_time) . '000') : false,
            'isBomber' => strpos($this->qualifications, 'bomber') !== false,
            'is_clanner' => intval($this->is_clanner),
        ];
    }

    public function getPrivateAttributes()
    {
        if (Yii::app()->user->id != $this->id)
            return [];
        return [
            'nickname' => $this->nickname,
            'firstname' => $this->firstname,
            'img_src' => $this->img_src,
            'canMakeOrders' => $this->canMakeOrders(),
            'canMakeNews' => $this->canMakeNews(),
            'isInstructor' => $this->isInstructor(),
            'fullname' => $this->nickname . ' (' . $this->firstname . ')',
            'broadcast_token' => $this->broadcast_token,
            'isDisabled' => intval($this->is_disabled),
            'disableReason' => $this->disable_reason,
            'uid' => $this->ts_id,
            'id' => $this->id
        ];
    }

    public function canMakeOrders()
    {
        return ($this->rank_id && $this->rank->order > 6);
    }

    public function canMakeNews()
    {
        return ($this->rank_id && $this->rank->order > 6);
    }

    public function isInstructor()
    {
        return $this->instructor_id ? true : false;
    }

    public static  function recover($email)
    {
        $user = User::model()->findByAttributes(['email'=>$email]);
        if (!$user)
            throw new Exception('Мы не можем восстановить этот аккаунт');
        $key = md5(microtime()+'champ');
        $user->recovery_token = $key;
        if (!$user->save())
           throw new Exception('Мы не можем восстановить этот аккаунт');
        Mailer::send($email, 'Восстановление пароля', Yii::app()->controller->renderPartial('//mails/password_recovery', compact('key'), true));
    }

    public static function isRecoveryTokenOK($token)
    {
        if(strlen(trim($token))!=32)
            throw new Exception('Token Invalid');

        return User::model()->exists([
            'condition' => 'recovery_token=:token',
            'params' => [':token' => $token]]);
    }

    public static function resetPassword($token, $password)
    {
        if(strlen(trim($token))!=32)
            throw new Exception('Token Invalid');
        $user = User::model()->find([
            'condition' => 'recovery_token=:token',
            'params' => [':token' => $token]]);

        if (!$user)
            throw new Exception('Token Invalid');
        $user->password = md5($password);
        $user->recovery_token = null;
        if (!$user->validate())
            throw new Exception($user->getErrors());
        if (!$user->save())
            throw new Exception('Возникла непредвиденная ошибка, мы над этим работаем...');
        return $user;
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
        $newUser->rank_id = 8;
        $newUser->is_clanner = (isset($user['squad']) && $user['squad']) ? 1 : 0;
        $newUser->roster = json_encode($user);
        $newUser->broadcast_token = md5($newUser->email.$newUser->password);

        if (!$newUser->validate())
            throw new Exception($newUser->getErrors());

        if (!$newUser->save())
            throw new Exception('Возникла непредвиденная ошибка, мы над этим работаем...');

        Mailer::send('luftwaffeschule@gmail.com', 'Новый пользователь', Yii::app()->controller->renderPartial('//mails/new_user', compact('newUser'), true));
        Mailer::send($newUser->email, 'Добро пожаловать в виртуальную школу пилотов', Yii::app()->controller->renderPartial('//mails/welcome', compact('newUser'), true));
        return $newUser;
    }

    public function syncWithTeamSpeak()
    {
        $dbId = Yii::app()->ts->ts3Server->clientFindDb($this->ts_id, true);
        if (count($dbId)) {
            $dbId = $dbId[0];
            $groups = Yii::app()->ts->ts3Server->clientGetServerGroupsByDbid($dbId);
            $ignoreRank = false;
            $ignoreInstructor = false;
            $ignoreBomberQualification = false;
            $needsBomberBadge = (strpos($this->qualifications, "bomber") !== false) && !in_array($this->instructor_id, ["30", "34", "35"]);
            $i = 1;
            do {
                $nicknameInUse = false;
                try {
                    Yii::app()->ts->setName('Отдел кадров №' . $i);
                } catch (Exception $e) {
                    if ($e->getMessage() == 'nickname is already in use')
                        $nicknameInUse = true;
                }
                $i++;
            } while ($nicknameInUse && ($i < 20));

            foreach ($groups as $groupId => $dummy) {
                if ($groupId == 8 || $groupId == 6)
                    continue;
                if ($groupId == $this->rank_id) {
                    $ignoreRank = true;
                    continue;
                }
                if ($groupId == $this->instructor_id) {
                    $ignoreInstructor = true;
                    continue;
                }

                if (($groupId == 36) && $needsBomberBadge) {
                    $ignoreBomberQualification = true;
                    continue;
                }

                Yii::app()->ts->ts3Server->serverGroupClientDel($groupId, $dbId);
            }
            if (!$ignoreRank)
                Yii::app()->ts->ts3Server->serverGroupClientAdd($this->rank_id, $dbId);

            if (!$ignoreInstructor && $this->instructor_id)
                Yii::app()->ts->ts3Server->serverGroupClientAdd($this->instructor_id, $dbId);

            if (!$ignoreBomberQualification && $needsBomberBadge)
                Yii::app()->ts->ts3Server->serverGroupClientAdd("36", $dbId);
        } else {
            throw new Exception('Пользователь не прикреплён к TeamSpeak');
        }
        NodeServerSync::sendInternalMessage("RELOAD_USER_LIST");
    }

    public function accept($tsId)
    {
        $this->ts_id = $tsId;
        $this->rank_id = 8;
        if (!$this->save())
            throw new Exception($this->getErrorsString());

        $data = [
            'complete' => '<p><a pilot="' . $this->id . '">' . $this->nickname
                . '</a> зачислен на <a rank="7">1-й Курс</a> в начальную школу пилотов</p>',
            'pilots' =>
                [
                    [
                        'id' => $this->id,
                        'rank' => 7
                    ]
                ]
        ];
        Order::issueOrder($data);
    }

    public function promoteCourse($courseId, $promoteToOfficer)
    {
        $course = Course::model()->findByPk($courseId);
        if (!$course)
            throw new Exception('Course not found');

        $afterText = ' в связи с успешной сдачей экзаменов';
        $eventText = 'В связи с успешной сдачей экзаменов';

        if ($course->next_rank_id == 16) {
            $afterText = '';
            $eventText = 'В связи с окончанием школы пилотов';
            if (!$promoteToOfficer) {
                $rank = 29;
                $text = ' окончил школу пилотов, присвоено звание <a rank="29">Выпускник</a>, выдан значок об окончании школы';
            } else {
                $rank = $course->nextRank->id;
                $text = ' окончил школу пилотов, присвоено звание <a rank="' . $course->nextRank->id . '">' . $course->nextRank->name . '</a>, выдан <a award="41">значок об окончании школы</a>';
            }
        } else {
            $rank = $course->next_rank_id;
            $text = ' переведён на <a rank="' . $course->nextRank->id . '">' . $course->nextRank->name . '</a>';
        }

        $data = [
            'event' => $eventText,
            'complete' => '<p><a pilot="' . $this->id . '">Курсант ' . $this->nickname
                . '</a>' . $text . $afterText . '</p>',
            'pilots' =>
                [
                    [
                        'id' => $this->id,
                        'rank' => $rank,
                    ]
                ]
        ];
        Order::issueOrder($data);
    }

    public function sendNotification($event, $data)
    {

        NodeServerSync::sendMessage($event ,$data, $this->broadcast_token);
        if ($this->ts_id)
          NodeServerSync::sendInternalMessage('NOTIFY_USER',['reciever'=>$this->ts_id,'msg'=>$data['summary']]);
    }

    public function reject($reason)
    {
        if ($this->ts_id)
            throw new Exception('Пользователь уже был принят!');
        $this->delete();
        Mailer::send($this->email, 'Заявка отклонена', Yii::app()->controller->renderPartial('//mails/user_reject', ['reason'=>$reason,'user'=>$this], true));
        Mailer::send('luftwaffeschule@gmail.com', 'Отклонение заявки', Yii::app()->controller->renderPartial('//mails/user_reject_notify', ['reason'=>$reason,'user'=>$this], true));
    }

    public function expel($reason)
    {
        if ($this->is_disabled)
            throw new Exception('Пользователь уже исключен!');
        $this->is_disabled = 1;
        $this->disable_reason = $reason;
        $this->save();
        Order::issueOrder([
            'complete'=>'<a rank="'.$this->rank_id.'">'.$this->rank->name.'</a> <a pilot="'.$this->id.'">'.$this->nickname.'</a> исключен из школы пилотов по причине: <p>'.$reason.'</p>',
            'pilots'=>[['id'=>$this->id]],
            'event'=>'Исключен из школы пилотов по причине: <p>'.$reason.'</p>'
        ]);
        Mailer::send($this->email, 'Вы исключенны', Yii::app()->controller->renderPartial('//mails/user_expel', ['reason' => $reason, 'user' => $this], true));
        Mailer::send('luftwaffeschule@gmail.com', 'Пилот исключён', Yii::app()->controller->renderPartial('//mails/user_expel_notify', ['reason' => $reason, 'user' => $this], true));
    }


    public function updateOnlineTime()
    {
        $this->last_online_time = date("Y-m-d H:i:s");
        if ($this->is_defector)
        {
            $this->is_defector = false;
            Mailer::send('luftwaffeschule@gmail.com', 'Дезертир вернулся', Yii::app()->controller->renderPartial('//mails/user_defector_return_notify', ['user' => $this], true));
        }
        $this->save();
    }

    /**
     * @return User
     */
    public function scopeNeedWarning()
    {
        $this->dbCriteria->mergeWith([
            'condition' => '(`last_warning_time` < NOW() - INTERVAL 1 MONTH) OR `last_warning_time` IS NULL'
        ]);
        return $this;
    }

    /**
     * @return User
     */
    public function scopeDisabled()
    {
        $this->dbCriteria->mergeWith([
            'condition' => 'is_disabled=1'
        ]);
        return $this;
    }

    /**
     * @return User
     */
    public function scopeActive()
    {
        $this->dbCriteria->mergeWith([
            'condition' => 'activeVacation.id IS NULL'
        ]);
        return $this;
    }

    /**
     * @return User
     */
    public function scopeVacation()
    {
        $this->dbCriteria->mergeWith([
            'condition' => 'activeVacation.id IS NOT NULL'
        ]);
        return $this;
    }


    protected function beforeDelete()
    {
        PrivateMessage::model()->deleteAll(['condition' => 'reciever_id=:id OR sender_id=:id', 'params' => [':id' => $this->id]]);
        return parent::beforeDelete();
    }
}