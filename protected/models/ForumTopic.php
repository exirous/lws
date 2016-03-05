<?php

Yii::import('application.models._base.BaseForumTopic');
/**
 * @method ForumTopic find
 * @method ForumTopic[] findAll
 * @method ForumTopic findByPk
 * @method ForumTopic[] findAllByPk
 * @method ForumTopic findByAttributes
 * @method ForumTopic[] findAllByAttributes
 * @method ForumTopic findBySql
 * @method ForumTopic[] findAllBySql
 * @method ForumTopic cache
 * @method ForumTopic resetScope
 * @method ForumTopic with
 * @method ForumTopic together
 * @method ForumTopic populateRecord
 * @method ForumTopic scopeLimit
 * @method ForumTopic scopeOffset
 * @method ForumTopic scopeOrder
 * @method ForumTopic scopeAllColumns
 * @method ForumTopic scopeSelect
 * @method ForumTopic byName
 */
class ForumTopic extends BaseForumTopic
{
    /**
     * @static
     * @param string $className
     * @return ForumTopic
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        $defaults = [
            ['time',
                'default',
                'value' => date("Y-m-d H:i:s")
            ]
        ];
        return CMap::mergeArray($defaults, parent::rules());
    }


    public function getRenderAttributes()
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'author'=>$this->author_id ? $this->author->getListAttributes() : null,
            'itemCount'=>ForumMessage::model()->scopeByTopic($this->id)->count(),
            'lastMessage'=>$this->last_message_id ? $this->lastMessage->getShortAttributes() : null,
            'firstMessageText'=>$this->firstMessage->text,
            'lastMessageTime'=>strtotime($this->last_message_id ? $this->lastMessage->time : $this->time),
        ];
    }

    public function afterSave()
    {
        parent::afterSave();
        Update::setUpdates('topic_' . $this->id, Yii::app()->user->id);
    }
}