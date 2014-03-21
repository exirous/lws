<?php

/**
 * This is the model base class for the table "user_group".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "UserGroup".
 *
 * Columns in table "user_group" available as properties of the model,
 * and there are no model relations.
 *
 * @property string $user_id
 * @property string $group_id
 *
 */
abstract class BaseUserGroup extends AActiveRecord
{

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'user_group';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'UserGroup|UserGroups', $n);
    }

    public static function representingColumn()
    {
        return array(
            'user_id',
            'group_id',
        );
    }

    public function rules()
    {
        return array(
            array('user_id, group_id', 'required'),
            array('user_id, group_id', 'length', 'max'=>10),
            array('user_id, group_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
        );
    }

    public function pivotModels()
    {
        return array(
        );
    }

    public function attributeLabels()
    {
        return array(
            'user_id' => null,
            'group_id' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('group_id', $this->group_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}