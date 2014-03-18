<?php

Yii::import('application.models._base.BaseNews');
/**
 * @method News find
 * @method News[] findAll
 * @method News findByPk
 * @method News[] findAllByPk
 * @method News findByAttributes
 * @method News[] findAllByAttributes
 * @method News findBySql
 * @method News[] findAllBySql
 * @method News cache
 * @method News resetScope
 * @method News with
 * @method News together
 * @method News populateRecord
 * @method News scopeLimit
 * @method News scopeOffset
 * @method News scopeOrder
 * @method News scopeAllColumns
 * @method News scopeSelect
 * @method News byName
 */
class News extends BaseNews
{
    /**
     * @static
     * @param string $className
     * @return News
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}