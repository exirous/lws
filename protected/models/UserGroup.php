<?php

Yii::import('application.models._base.BaseUserGroup');
/**
 * @method UserGroup find
 * @method UserGroup[] findAll
 * @method UserGroup findByPk
 * @method UserGroup[] findAllByPk
 * @method UserGroup findByAttributes
 * @method UserGroup[] findAllByAttributes
 * @method UserGroup findBySql
 * @method UserGroup[] findAllBySql
 * @method UserGroup cache
 * @method UserGroup resetScope
 * @method UserGroup with
 * @method UserGroup together
 * @method UserGroup populateRecord
 * @method UserGroup scopeLimit
 * @method UserGroup scopeOffset
 * @method UserGroup scopeOrder
 * @method UserGroup scopeAllColumns
 * @method UserGroup scopeSelect
 * @method UserGroup byName
 */
class UserGroup extends BaseUserGroup
{
    /**
     * @static
     * @param string $className
     * @return UserGroup
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}