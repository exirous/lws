<?php

Yii::import('application.models._base.BaseUpdate');

/**
 * @method Update find
 * @method Update[] findAll
 * @method Update findByPk
 * @method Update[] findAllByPk
 * @method Update findByAttributes
 * @method Update[] findAllByAttributes
 * @method Update findBySql
 * @method Update[] findAllBySql
 * @method Update cache
 * @method Update resetScope
 * @method Update with
 * @method Update together
 * @method Update populateRecord
 * @method Update scopeLimit
 * @method Update scopeOffset
 * @method Update scopeOrder
 * @method Update scopeAllColumns
 * @method Update scopeSelect
 * @method Update byName
 */
class Update extends BaseUpdate
{
    /**
     * @static
     * @param string $className
     * @return Update
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function setUpdates($section, $exeptUserId)
    {
        foreach (User::model()->scopeEnabled()->findAll() as $user) {
            if ($user->id == $exeptUserId)
                continue;
            $update = Update::model()->find(['condition' => 'user_id=:userId AND section=:section', 'params' => ['userId' => $user->id, 'section' => $section]]);
            if (!$update) {
                $update = new Update();
                $update->date = date("Y-m-d H:i:s");
                $update->user_id = $user->id;
                $update->section = $section;
                $update->save();
                $user->sendNotification('section_update', $update->getAttributes(['id', 'section', 'date']));
            }
        }
    }
}