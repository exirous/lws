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
}