<?php

Yii::import('application.models._base.BasePrivateMessage');

/**
 * @method PrivateMessage find
 * @method PrivateMessage[] findAll
 * @method PrivateMessage findByPk
 * @method PrivateMessage[] findAllByPk
 * @method PrivateMessage findByAttributes
 * @method PrivateMessage[] findAllByAttributes
 * @method PrivateMessage findBySql
 * @method PrivateMessage[] findAllBySql
 * @method PrivateMessage cache
 * @method PrivateMessage resetScope
 * @method PrivateMessage with
 * @method PrivateMessage together
 * @method PrivateMessage populateRecord
 * @method PrivateMessage scopeLimit
 * @method PrivateMessage scopeOffset
 * @method PrivateMessage scopeOrder
 * @method PrivateMessage scopeAllColumns
 * @method PrivateMessage scopeSelect
 * @method PrivateMessage byName
 */
class PrivateMessage extends BasePrivateMessage
{
    /**
     * @static
     * @param string $className
     * @return PrivateMessage
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

    public function scopeMeAndHim($id)
    {
        $this->dbCriteria->mergeWith([
            'condition' => '(sender_id = :me AND reciever_id = :sender AND is_deleted_by_sender IS NULL) OR (sender_id = :sender AND reciever_id = :me AND is_deleted_by_reciever IS NULL)',
            'params' => ['me' => intval(Yii::app()->user->model->id), 'sender' => intval($id)]
        ]);
        return $this;
    }

    public function getRenderAttributes()
    {
        return [
            'id'=>$this->id,
            'text'=>$this->text,
            'is_read'=>$this->is_read,
            'sender'=>$this->sender_id ? $this->sender->getListAttributes() : null,
            'time'=>strtotime($this->time).'000'
        ];
    }

    public function notify()
    {
        require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
        $parser = new JBBCode\Parser();
        $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
        $data = $this->getRenderAttributes();
        $parser->parse($data['text']);
        $data['text'] = nl2br($parser->getAsHTML());
        $data['summary'] = "У вас новое сообщение от [b]" . $this->sender->nickname . "[/b]\n [url]http://luftwaffeschule.ru/#/conversation/" . $this->sender_id . "/page-1[/url]";
        $this->reciever->sendNotification('new_message',$data, true);
    }

}