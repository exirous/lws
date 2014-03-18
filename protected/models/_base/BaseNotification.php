<?php

/**
 * This is the model base class for the table "notification".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Notification".
 *
 * Columns in table "notification" available as properties of the model,
 * followed by relations of table "notification" available as properties of the model.
 *
 * @property string $id
 * @property string $user_id
 * @property string $time
 * @property string $data
 * @property integer $viewed
 *
 * @property User $user
 */
abstract class BaseNotification extends AActiveRecord
{

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'notification';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'Notification|Notifications', $n);
    }

    public static function representingColumn()
    {
        return 'time';
    }

    public function rules()
    {
        return array(
            array('user_id, time', 'required'),
            array('viewed', 'numerical', 'integerOnly'=>true),
            array('user_id', 'length', 'max'=>10),
            array('data', 'length', 'max'=>255),
            array('data, viewed', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, user_id, time, data, viewed', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
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
            'user_id' => null,
            'time' => Yii::t('app', 'Time'),
            'data' => Yii::t('app', 'Data'),
            'viewed' => Yii::t('app', 'Viewed'),
            'user' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('time', $this->time, true);
        $criteria->compare('data', $this->data, true);
        $criteria->compare('viewed', $this->viewed);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}