<?php

Yii::import('application.models._base.BaseOrderParticipants');
/**
 * @method OrderParticipants find
 * @method OrderParticipants[] findAll
 * @method OrderParticipants findByPk
 * @method OrderParticipants[] findAllByPk
 * @method OrderParticipants findByAttributes
 * @method OrderParticipants[] findAllByAttributes
 * @method OrderParticipants findBySql
 * @method OrderParticipants[] findAllBySql
 * @method OrderParticipants cache
 * @method OrderParticipants resetScope
 * @method OrderParticipants with
 * @method OrderParticipants together
 * @method OrderParticipants populateRecord
 * @method OrderParticipants scopeLimit
 * @method OrderParticipants scopeOffset
 * @method OrderParticipants scopeOrder
 * @method OrderParticipants scopeAllColumns
 * @method OrderParticipants scopeSelect
 * @method OrderParticipants byName
 */
class OrderParticipants extends BaseOrderParticipants
{
    /**
     * @static
     * @param string $className
     * @return OrderParticipants
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}