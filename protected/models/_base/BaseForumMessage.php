<?php

/**
 * This is the model base class for the table "forum_message".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "ForumMessage".
 *
 * Columns in table "forum_message" available as properties of the model,
 * followed by relations of table "forum_message" available as properties of the model.
 *
 * @property string $id
 * @property string $topic_id
 * @property string $author_id
 * @property string $text
 * @property string $time
 *
 * @property User $author
 * @property ForumTopic $topic
 * @property ForumTopic[] $forumTopics
 * @property ForumTopic[] $forumTopics1
 */
abstract class BaseForumMessage extends AActiveRecord
{

    public function getTextColumns()
    {
        return array();
    }

    public function tableName()
    {
        return 'forum_message';
    }

    public static function label($n = 1)
    {
        return Yii::t('admin', 'ForumMessage|ForumMessages', $n);
    }

    public static function representingColumn()
    {
        return 'text';
    }

    public function rules()
    {
        return array(
            array('topic_id, text, time', 'required'),
            array('topic_id, author_id', 'length', 'max'=>10),
            array('author_id', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, topic_id, author_id, text, time', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'author' => array(self::BELONGS_TO, 'User', 'author_id'),
            'topic' => array(self::BELONGS_TO, 'ForumTopic', 'topic_id'),
            'forumTopics' => array(self::HAS_MANY, 'ForumTopic', 'first_message_id'),
            'forumTopics1' => array(self::HAS_MANY, 'ForumTopic', 'last_message_id'),
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
            'topic_id' => null,
            'author_id' => null,
            'text' => Yii::t('app', 'Text'),
            'time' => Yii::t('app', 'Time'),
            'author' => null,
            'topic' => null,
            'forumTopics' => null,
            'forumTopics1' => null,
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('topic_id', $this->topic_id);
        $criteria->compare('author_id', $this->author_id);
        $criteria->compare('text', $this->text, true);
        $criteria->compare('time', $this->time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}