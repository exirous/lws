<?php

Yii::import('application.models._base.BaseText');

/**
 * @method Text find
 * @method Text[] findAll
 * @method Text findByPk
 * @method Text[] findAllByPk
 * @method Text findByAttributes
 * @method Text[] findAllByAttributes
 * @method Text findBySql
 * @method Text[] findAllBySql
 * @method Text cache
 * @method Text resetScope
 * @method Text with
 * @method Text together
 * @method Text populateRecord
 * @method Text scopeLimit
 * @method Text scopeOffset
 * @method Text scopeOrder
 * @method Text scopeAllColumns
 * @method Text scopeSelect
 * @method Text byName
 */
class Text extends BaseText
{
    /**
     * @static
     * @param string $className
     * @return Text
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getRenderAttributes()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'text' => $this->text
        ];
    }

}