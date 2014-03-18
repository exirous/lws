<?php

Yii::import('application.models._base.BaseAward');
/**
 * @method Award find
 * @method Award[] findAll
 * @method Award findByPk
 * @method Award[] findAllByPk
 * @method Award findByAttributes
 * @method Award[] findAllByAttributes
 * @method Award findBySql
 * @method Award[] findAllBySql
 * @method Award cache
 * @method Award resetScope
 * @method Award with
 * @method Award together
 * @method Award populateRecord
 * @method Award scopeLimit
 * @method Award scopeOffset
 * @method Award scopeOrder
 * @method Award scopeAllColumns
 * @method Award scopeSelect
 * @method Award byName
 */
class Award extends BaseAward
{
    /**
     * @static
     * @param string $className
     * @return Award
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}