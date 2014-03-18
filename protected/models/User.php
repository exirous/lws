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
}