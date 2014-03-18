<?php

Yii::import('application.models._base.BaseGroup');
/**
 * @method Group find
 * @method Group[] findAll
 * @method Group findByPk
 * @method Group[] findAllByPk
 * @method Group findByAttributes
 * @method Group[] findAllByAttributes
 * @method Group findBySql
 * @method Group[] findAllBySql
 * @method Group cache
 * @method Group resetScope
 * @method Group with
 * @method Group together
 * @method Group populateRecord
 * @method Group scopeLimit
 * @method Group scopeOffset
 * @method Group scopeOrder
 * @method Group scopeAllColumns
 * @method Group scopeSelect
 * @method Group byName
 */
class Group extends BaseGroup
{
    /**
     * @static
     * @param string $className
     * @return Group
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}