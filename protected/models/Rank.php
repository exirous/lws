<?php

Yii::import('application.models._base.BaseRank');
/**
 * @method Rank find
 * @method Rank[] findAll
 * @method Rank findByPk
 * @method Rank[] findAllByPk
 * @method Rank findByAttributes
 * @method Rank[] findAllByAttributes
 * @method Rank findBySql
 * @method Rank[] findAllBySql
 * @method Rank cache
 * @method Rank resetScope
 * @method Rank with
 * @method Rank together
 * @method Rank populateRecord
 * @method Rank scopeLimit
 * @method Rank scopeOffset
 * @method Rank scopeOrder
 * @method Rank scopeAllColumns
 * @method Rank scopeSelect
 * @method Rank byName
 * @property Array shortAttributes
 */
class Rank extends BaseRank
{
    /**
     * @static
     * @param string $className
     * @return Rank
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getShortAttributes()
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'order'=>intval($this->order)
        ];
    }

    public function defaultScope()
    {
        return array(
            'order'=>'`order`',
        );
    }

    /**
     * @return $this
     */
    public function scopeRanks()
    {
        $this->dbCriteria->mergeWith([
            'condition'=>'type=:type',
            'params'=>['type'=>self::TYPE_RANK]
        ]);
        return $this;
    }

    public function scopeCorrectOrder()
    {
        $this->dbCriteria->mergeWith([
            'order'=>'`order`'
        ]);
        return $this;
    }


    public function scopeInstructors()
    {
        $this->dbCriteria->mergeWith([
            'condition'=>'type=:type',
            'params'=>['type'=>self::TYPE_INSTRUCTOR]
        ]);
        return $this;
    }


}