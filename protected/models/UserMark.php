<?php

Yii::import('application.models._base.BaseUserMark');

/**
 * @method UserMark find
 * @method UserMark[] findAll
 * @method UserMark findByPk
 * @method UserMark[] findAllByPk
 * @method UserMark findByAttributes
 * @method UserMark[] findAllByAttributes
 * @method UserMark findBySql
 * @method UserMark[] findAllBySql
 * @method UserMark cache
 * @method UserMark resetScope
 * @method UserMark with
 * @method UserMark together
 * @method UserMark populateRecord
 * @method UserMark scopeLimit
 * @method UserMark scopeOffset
 * @method UserMark scopeOrder
 * @method UserMark scopeAllColumns
 * @method UserMark scopeSelect
 * @method UserMark byName
 */
class UserMark extends BaseUserMark
{
    /**
     * @static
     * @param string $className
     * @return UserMark
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        return [
            'order' => 'time'
        ];
    }

    public function rules()
    {
        $defaults = [
            ['time',
                'default',
                'value' => gmdate("Y-m-d H:i:s")
            ]
        ];
        return CMap::mergeArray($defaults, parent::rules());
    }


    public function getPublicAttributes()
    {
        return [
            'subject' => $this->subject_id,
            'mark' => $this->mark,
            'time' => $this->time,
        ];
    }

    public static function saveMark($userId, $subjectId, $mark)
    {
        $mark = intval($mark);
        if ($mark > 5 || $mark < 1)
            throw new Exception('Mark must be within 5 and 1');
        $userMark = new UserMark();
        $userMark->user_id = $userId;
        $userMark->subject_id = $subjectId;
        $userMark->mark = $mark;
        $userMark->issuer_id = Yii::app()->user->model->id;
        if (!$userMark->save())
        {
            throw new Exception($userMark->getErrorsString());
        }
    }

}