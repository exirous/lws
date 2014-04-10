<?php

Yii::import('application.models._base.BaseOrder');
/**
 * @method Order find
 * @method Order[] findAll
 * @method Order findByPk
 * @method Order[] findAllByPk
 * @method Order findByAttributes
 * @method Order[] findAllByAttributes
 * @method Order findBySql
 * @method Order[] findAllBySql
 * @method Order cache
 * @method Order resetScope
 * @method Order with
 * @method Order together
 * @method Order populateRecord
 * @method Order scopeLimit
 * @method Order scopeOffset
 * @method Order scopeOrder
 * @method Order scopeAllColumns
 * @method Order scopeSelect
 * @method Order byName
 */
class Order extends BaseOrder
{
    /**
     * @static
     * @param string $className
     * @return Order
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function renderAttributes()
    {
        $time = strtotime($this->time);
        return [
            'title'=>'Выдан Приказ',
            'time' => date('d.m.Y',$time),
            'timepar' => $time,
            'type' => 'order',
            'id' => $this->id,
            'text' => $this->text,
            'issuer' => ['id' => $this->issuer_id, 'name' => $this->issuer->nickname]
        ];
    }
}