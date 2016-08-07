<?php

/**
 * This is the model base class for the table "battle_event".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "BattleEvent".
 *
 * Columns in table "battle_event" available as properties of the model,
 * followed by relations of table "battle_event" available as properties of the model.
 *
 * @property string $id
 * @property string $user_id
 * @property string $mission
 * @property integer $flight_time
 * @property string $time
 * @property string $air_targets
 * @property string $ground_targets
 * @property string $fine_points
 * @property string $fine_points_times
 * @property string $result
 *
 * @property User $user
 */
abstract class BaseBattleEvent extends AActiveRecord
{

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'battle_event';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'BattleEvent|BattleEvents', $n);
    }

    public static function representingColumn()
    {
        return 'mission';
    }

    public function rules()
    {
        return array(
            array('user_id', 'required'),
            array('flight_time', 'numerical', 'integerOnly'=>true),
            array('user_id', 'length', 'max'=>10),
            array('air_targets, ground_targets, fine_points, fine_points_times', 'length', 'max'=>3),
            array('mission, time, result', 'safe'),
            array('mission, flight_time, time, air_targets, ground_targets, fine_points, fine_points_times, result', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, user_id, mission, flight_time, time, air_targets, ground_targets, fine_points, fine_points_times, result', 'safe', 'on' => 'search'),
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
            'mission' => Yii::t('app', 'Mission'),
            'flight_time' => Yii::t('app', 'Flight Time'),
            'time' => Yii::t('app', 'Time'),
            'air_targets' => Yii::t('app', 'Air Targets'),
            'ground_targets' => Yii::t('app', 'Ground Targets'),
            'fine_points' => Yii::t('app', 'Fine Points'),
            'fine_points_times' => Yii::t('app', 'Fine Points Times'),
            'result' => Yii::t('app', 'Result'),
            'user' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('mission', $this->mission, true);
        $criteria->compare('flight_time', $this->flight_time);
        $criteria->compare('time', $this->time, true);
        $criteria->compare('air_targets', $this->air_targets, true);
        $criteria->compare('ground_targets', $this->ground_targets, true);
        $criteria->compare('fine_points', $this->fine_points, true);
        $criteria->compare('fine_points_times', $this->fine_points_times, true);
        $criteria->compare('result', $this->result, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}