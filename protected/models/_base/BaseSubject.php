<?php

/**
 * This is the model base class for the table "subject".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Subject".
 *
 * Columns in table "subject" available as properties of the model,
 * followed by relations of table "subject" available as properties of the model.
 *
 * @property string $id
 * @property string $name
 * @property string $course_id
 *
 * @property Material[] $materials
 * @property Course $course
 * @property UserMark[] $userMarks
 */
abstract class BaseSubject extends AActiveRecord
{

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'subject';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'Subject|Subjects', $n);
    }

    public static function representingColumn()
    {
        return 'name';
    }

    public function rules()
    {
        return array(
            array('course_id', 'required'),
            array('name', 'length', 'max'=>128),
            array('course_id', 'length', 'max'=>10),
            array('name', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, name, course_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'materials' => array(self::HAS_MANY, 'Material', 'subject_id'),
            'course' => array(self::BELONGS_TO, 'Course', 'course_id'),
            'userMarks' => array(self::HAS_MANY, 'UserMark', 'subject_id'),
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
            'course_id' => null,
            'materials' => null,
            'course' => null,
            'userMarks' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('course_id', $this->course_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}