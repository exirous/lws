<?php

Yii::import('application.models._base.BaseMaterial');
/**
 * @method Material find
 * @method Material[] findAll
 * @method Material findByPk
 * @method Material[] findAllByPk
 * @method Material findByAttributes
 * @method Material[] findAllByAttributes
 * @method Material findBySql
 * @method Material[] findAllBySql
 * @method Material cache
 * @method Material resetScope
 * @method Material with
 * @method Material together
 * @method Material populateRecord
 * @method Material scopeLimit
 * @method Material scopeOffset
 * @method Material scopeOrder
 * @method Material scopeAllColumns
 * @method Material scopeSelect
 * @method Material byName
 */
class Material extends BaseMaterial
{
    /**
     * @static
     * @param string $className
     * @return Material
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}