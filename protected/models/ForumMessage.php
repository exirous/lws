<?php

Yii::import('application.models._base.BaseForumMessage');
/**
 * @method ForumMessage find
 * @method ForumMessage[] findAll
 * @method ForumMessage findByPk
 * @method ForumMessage[] findAllByPk
 * @method ForumMessage findByAttributes
 * @method ForumMessage[] findAllByAttributes
 * @method ForumMessage findBySql
 * @method ForumMessage[] findAllBySql
 * @method ForumMessage cache
 * @method ForumMessage resetScope
 * @method ForumMessage with
 * @method ForumMessage together
 * @method ForumMessage populateRecord
 * @method ForumMessage scopeLimit
 * @method ForumMessage scopeOffset
 * @method ForumMessage scopeOrder
 * @method ForumMessage scopeAllColumns
 * @method ForumMessage scopeSelect
 * @method ForumMessage byName
 */
class ForumMessage extends BaseForumMessage
{
    /**
     * @static
     * @param string $className
     * @return ForumMessage
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


    public function scopeByTopic($id)
    {
        $this->dbCriteria->mergeWith([
            'condition'=>'topic_id=:topicId',
            'params'=>['topicId'=>$id]
        ]);
        return $this;
    }

    public function getRenderAttributes()
    {
        return [
            'id'=>$this->id,
            'text'=>$this->text,
            'author'=>$this->author_id ? $this->author->getListAttributes() : null,
            'time'=>strtotime($this->time).'000'
        ];
    }
    public function getShortAttributes()
    {
        return [
            'id'=>$this->id,
            'author'=>$this->author_id ? $this->author->getListAttributes() : null,
            'time'=>strtotime($this->time).'000'
        ];
    }

    public function afterSave()
    {
        parent::afterSave();
        Update::setUpdates('topic_' . $this->topic_id, Yii::app()->user->id);
    }

}