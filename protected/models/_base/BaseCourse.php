<?php

/**
 * This is the model base class for the table "course".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Course".
 *
 * Columns in table "course" available as properties of the model,
 * followed by relations of table "course" available as properties of the model.
 *
 * @property string $id
 * @property string $name
 * @property string $program_id
 * @property string $group_id
 *
 * @property Group $group
 * @property Program $program
 * @property Subject[] $subjects
 */
abstract class BaseCourse extends AActiveRecord
{

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'course';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'Course|Courses', $n);
    }

    public static function representingColumn()
    {
        return 'name';
    }

    public function rules()
    {
        return array(
            array('program_id', 'required'),
            array('name', 'length', 'max'=>64),
            array('program_id, group_id', 'length', 'max'=>10),
            array('name, group_id', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, name, program_id, group_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'group' => array(self::BELONGS_TO, 'Group', 'group_id'),
            'program' => array(self::BELONGS_TO, 'Program', 'program_id'),
            'subjects' => array(self::HAS_MANY, 'Subject', 'course_id'),
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
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'program_id' => null,
            'group_id' => null,
            'group' => null,
            'program' => null,
            'subjects' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('program_id', $this->program_id);
        $criteria->compare('group_id', $this->group_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}