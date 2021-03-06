<?php

/**
 * This is the model base class for the table "group".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Group".
 *
 * Columns in table "group" available as properties of the model,
 * followed by relations of table "group" available as properties of the model.
 *
 * @property string $id
 * @property string $name
 * @property string $permissions
 * @property string $parent_group_id
 * @property string $ts_group_id
 *
 * @property Course[] $courses
 * @property Group $parentGroup
 * @property Group[] $groups
 * @property User[] $users
 */
abstract class BaseGroup extends AActiveRecord
{
    const PERMISSIONS_ADMIN = 'admin';

    public function getSetValueText($column, $value = null)
    {
        $texts = array(
            'permissions' => array(
                'admin' => Yii::t('app', 'Admin'),
            ),
        );

        if ($value)
            return $texts[$column][$value];

        return $texts[$column];
    }

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'group';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'Group|Groups', $n);
    }

    public static function representingColumn()
    {
        return 'name';
    }

    public function rules()
    {
        return array(
            array('name', 'length', 'max'=>32),
            array('parent_group_id, ts_group_id', 'length', 'max'=>10),
            array('permissions', 'safe'),
            array('name, permissions, parent_group_id, ts_group_id', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, name, permissions, parent_group_id, ts_group_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'courses' => array(self::HAS_MANY, 'Course', 'group_id'),
            'parentGroup' => array(self::BELONGS_TO, 'Group', 'parent_group_id'),
            'groups' => array(self::HAS_MANY, 'Group', 'parent_group_id'),
            'users' => array(self::MANY_MANY, 'User', 'user_group(group_id, user_id)'),
        );
    }

    public function pivotModels()
    {
        return array(
            'users' => 'UserGroup',
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'permissions' => Yii::t('app', 'Permissions'),
            'parent_group_id' => null,
            'ts_group_id' => Yii::t('app', 'Ts Group'),
            'courses' => null,
            'parentGroup' => null,
            'groups' => null,
            'users' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('permissions', $this->permissions, true);
        $criteria->compare('parent_group_id', $this->parent_group_id);
        $criteria->compare('ts_group_id', $this->ts_group_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}