<?php

Yii::import('application.models._base.BaseNotification');
/**
 * @method Notification find
 * @method Notification[] findAll
 * @method Notification findByPk
 * @method Notification[] findAllByPk
 * @method Notification findByAttributes
 * @method Notification[] findAllByAttributes
 * @method Notification findBySql
 * @method Notification[] findAllBySql
 * @method Notification cache
 * @method Notification resetScope
 * @method Notification with
 * @method Notification together
 * @method Notification populateRecord
 * @method Notification scopeLimit
 * @method Notification scopeOffset
 * @method Notification scopeOrder
 * @method Notification scopeAllColumns
 * @method Notification scopeSelect
 * @method Notification byName
 */
class Notification extends BaseNotification
{
    /**
     * @static
     * @param string $className
     * @return Notification
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}