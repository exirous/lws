<?php

/**
 * This is the model base class for the table "material".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Material".
 *
 * Columns in table "material" available as properties of the model,
 * followed by relations of table "material" available as properties of the model.
 *
 * @property string $id
 * @property string $subject_id
 * @property string $text
 *
 * @property Subject $subject
 */
abstract class BaseMaterial extends AActiveRecord
{

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'material';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'Material|Materials', $n);
    }

    public static function representingColumn()
    {
        return 'text';
    }

    public function rules()
    {
        return array(
            array('subject_id', 'length', 'max'=>10),
            array('text', 'safe'),
            array('subject_id, text', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, subject_id, text', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'subject' => array(self::BELONGS_TO, 'Subject', 'subject_id'),
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
            'subject_id' => null,
            'text' => Yii::t('app', 'Text'),
            'subject' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->compare('text', $this->text, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}