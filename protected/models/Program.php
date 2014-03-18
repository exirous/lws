<?php

Yii::import('application.models._base.BaseProgram');
/**
 * @method Program find
 * @method Program[] findAll
 * @method Program findByPk
 * @method Program[] findAllByPk
 * @method Program findByAttributes
 * @method Program[] findAllByAttributes
 * @method Program findBySql
 * @method Program[] findAllBySql
 * @method Program cache
 * @method Program resetScope
 * @method Program with
 * @method Program together
 * @method Program populateRecord
 * @method Program scopeLimit
 * @method Program scopeOffset
 * @method Program scopeOrder
 * @method Program scopeAllColumns
 * @method Program scopeSelect
 * @method Program byName
 */
class Program extends BaseProgram
{
    /**
     * @static
     * @param string $className
     * @return Program
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}