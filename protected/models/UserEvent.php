<?php

Yii::import('application.models._base.BaseUserEvent');
/**
 * @method UserEvent find
 * @method UserEvent[] findAll
 * @method UserEvent findByPk
 * @method UserEvent[] findAllByPk
 * @method UserEvent findByAttributes
 * @method UserEvent[] findAllByAttributes
 * @method UserEvent findBySql
 * @method UserEvent[] findAllBySql
 * @method UserEvent cache
 * @method UserEvent resetScope
 * @method UserEvent with
 * @method UserEvent together
 * @method UserEvent populateRecord
 * @method UserEvent scopeLimit
 * @method UserEvent scopeOffset
 * @method UserEvent scopeOrder
 * @method UserEvent scopeAllColumns
 * @method UserEvent scopeSelect
 * @method UserEvent byName
 */
class UserEvent extends BaseUserEvent
{
    /**
     * @static
     * @param string $className
     * @return UserEvent
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    public function defaultScope()
    {
        return [
            'order' =>'date, id'
        ];
    }


    public function getPublicAttributes()
    {
        return [
            'id'=>$this->id,
            'text'=>$this->text,
            'date'=>strtotime($this->date).'000'
        ];
    }
}