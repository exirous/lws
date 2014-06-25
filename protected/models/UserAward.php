<?php

Yii::import('application.models._base.BaseUserAward');
/**
 * @method UserAward find
 * @method UserAward[] findAll
 * @method UserAward findByPk
 * @method UserAward[] findAllByPk
 * @method UserAward findByAttributes
 * @method UserAward[] findAllByAttributes
 * @method UserAward findBySql
 * @method UserAward[] findAllBySql
 * @method UserAward cache
 * @method UserAward resetScope
 * @method UserAward with
 * @method UserAward together
 * @method UserAward populateRecord
 * @method UserAward scopeLimit
 * @method UserAward scopeOffset
 * @method UserAward scopeOrder
 * @method UserAward scopeAllColumns
 * @method UserAward scopeSelect
 * @method UserAward byName
 * @property Award award
 */
class UserAward extends BaseUserAward
{
    /**
     * @static
     * @param string $className
     * @return UserAward
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'award'=>array(self::BELONGS_TO, 'Award', 'award_id'),
        );
    }

}