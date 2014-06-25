<?php

Yii::import('application.models._base.BaseVacation');

/**
 * @method Vacation find
 * @method Vacation[] findAll
 * @method Vacation findByPk
 * @method Vacation[] findAllByPk
 * @method Vacation findByAttributes
 * @method Vacation[] findAllByAttributes
 * @method Vacation findBySql
 * @method Vacation[] findAllBySql
 * @method Vacation cache
 * @method Vacation resetScope
 * @method Vacation with
 * @method Vacation together
 * @method Vacation populateRecord
 * @method Vacation scopeLimit
 * @method Vacation scopeOffset
 * @method Vacation scopeOrder
 * @method Vacation scopeAllColumns
 * @method Vacation scopeSelect
 * @method Vacation byName
 */
class Vacation extends BaseVacation
{
    /**
     * @static
     * @param string $className
     * @return Vacation
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getPublicAttributes()
    {
        $dateFrom = strtotime($this->date_from);
        $dateTo = strtotime($this->date_to);
        $now = time();
        return [
            'id' => $this->id,
            'dateFrom' => $dateFrom . '000',
            'dateTo' => $dateTo . '000',
            'reason' => $this->reason,
            'active'=>($dateFrom < $now && $dateTo > $now) ? true : false
        ];
    }
}