<?php

Yii::import('application.models._base.BaseBattleEvent');

/**
 * @method BattleEvent find
 * @method BattleEvent[] findAll
 * @method BattleEvent findByPk
 * @method BattleEvent[] findAllByPk
 * @method BattleEvent findByAttributes
 * @method BattleEvent[] findAllByAttributes
 * @method BattleEvent findBySql
 * @method BattleEvent[] findAllBySql
 * @method BattleEvent cache
 * @method BattleEvent resetScope
 * @method BattleEvent with
 * @method BattleEvent together
 * @method BattleEvent populateRecord
 * @method BattleEvent scopeLimit
 * @method BattleEvent scopeOffset
 * @method BattleEvent scopeOrder
 * @method BattleEvent scopeAllColumns
 * @method BattleEvent scopeSelect
 * @method BattleEvent byName
 */
class BattleEvent extends BaseBattleEvent
{
    /**
     * @static
     * @param string $className
     * @return BattleEvent
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getForUser($id, $page, $perPage)
    {
        $from = $perPage * ($page - 1);
        $events = [];
        foreach (BattleEvent::model()->scopeOrder('time desc')->findAll(['condition' => 'user_id=:id', 'params' => ['id' => $id], 'limit' => $perPage, 'offset' => $from]) as $battleEvent) {
            $events[] = $battleEvent->getPublicAttributes();
        }
        $count = Yii::app()->db->createCommand('SELECT COUNT(*) FROM `battle_event` WHERE user_id=:id')->queryScalar(['id' => $id]);
        return ["records" => $events, 'count' => $count];
    }

    public function getPublicAttributes()
    {
        $attributes = $this->attributes;
        $attributes['time'] = strtotime($attributes['time']) . '000';
        return $attributes;
    }
}