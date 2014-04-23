<?php

Yii::import('application.models._base.BaseOrderParticipant');
/**
 * @method OrderParticipant find
 * @method OrderParticipant[] findAll
 * @method OrderParticipant findByPk
 * @method OrderParticipant[] findAllByPk
 * @method OrderParticipant findByAttributes
 * @method OrderParticipant[] findAllByAttributes
 * @method OrderParticipant findBySql
 * @method OrderParticipant[] findAllBySql
 * @method OrderParticipant cache
 * @method OrderParticipant resetScope
 * @method OrderParticipant with
 * @method OrderParticipant together
 * @method OrderParticipant populateRecord
 * @method OrderParticipant scopeLimit
 * @method OrderParticipant scopeOffset
 * @method OrderParticipant scopeOrder
 * @method OrderParticipant scopeAllColumns
 * @method OrderParticipant scopeSelect
 * @method OrderParticipant byName
 */
class OrderParticipant extends BaseOrderParticipant
{
    /**
     * @static
     * @param string $className
     * @return OrderParticipant
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}