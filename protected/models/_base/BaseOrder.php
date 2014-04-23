<?php

/**
 * This is the model base class for the table "order".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Order".
 *
 * Columns in table "order" available as properties of the model,
 * followed by relations of table "order" available as properties of the model.
 *
 * @property string $id
 * @property string $text
 * @property string $issuer_id
 * @property string $time
 *
 * @property User $issuer
 * @property User[] $users
 */
abstract class BaseOrder extends AActiveRecord
{

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'order';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'Order|Orders', $n);
    }

    public static function representingColumn()
    {
        return 'time';
    }

    public function rules()
    {
        return array(
            array('time', 'required'),
            array('issuer_id', 'length', 'max'=>10),
            array('text', 'safe'),
            array('text, issuer_id', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, text, issuer_id, time', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'issuer' => array(self::BELONGS_TO, 'User', 'issuer_id'),
            'users' => array(self::MANY_MANY, 'User', 'order_participant(order_id, user_id)'),
        );
    }

    public function pivotModels()
    {
        return array(
            'users' => 'OrderParticipant',
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('app', 'ID'),
            'text' => Yii::t('app', 'Text'),
            'issuer_id' => null,
            'time' => Yii::t('app', 'Time'),
            'issuer' => null,
            'users' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('text', $this->text, true);
        $criteria->compare('issuer_id', $this->issuer_id);
        $criteria->compare('time', $this->time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}