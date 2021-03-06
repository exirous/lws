<?php

/**
 * This is the model base class for the table "order_participant".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "OrderParticipant".
 *
 * Columns in table "order_participant" available as properties of the model,
 * and there are no model relations.
 *
 * @property string $user_id
 * @property string $order_id
 *
 */
abstract class BaseOrderParticipant extends AActiveRecord
{

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'order_participant';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'OrderParticipant|OrderParticipants', $n);
    }

    public static function representingColumn()
    {
        return array(
            'user_id',
            'order_id',
        );
    }

    public function rules()
    {
        return array(
            array('user_id, order_id', 'required'),
            array('user_id, order_id', 'length', 'max'=>10),
            array('user_id, order_id', 'safe', 'on' => 'search'),
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
            'order_id' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('order_id', $this->order_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}