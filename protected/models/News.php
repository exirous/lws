<?php

Yii::import('application.models._base.BaseNews');

/**
 * @method News find
 * @method News[] findAll
 * @method News findByPk
 * @method News[] findAllByPk
 * @method News findByAttributes
 * @method News[] findAllByAttributes
 * @method News findBySql
 * @method News[] findAllBySql
 * @method News cache
 * @method News resetScope
 * @method News with
 * @method News together
 * @method News populateRecord
 * @method News scopeLimit
 * @method News scopeOffset
 * @method News scopeOrder
 * @method News scopeAllColumns
 * @method News scopeSelect
 * @method News byName
 */
class News extends BaseNews
{
    /**
     * @static
     * @param string $className
     * @return News
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getLast()
    {
        $lastNews = [];
        foreach (News::model()->findAll(['order' => 'id desc', 'limit' => 8]) as $news)
            $lastNews[] = $news->renderAttributes();

        foreach (Order::model()->findAll(['order' => 'id desc', 'limit' => 8]) as $order)
            $lastNews[] = $order->renderAttributes();

        usort($lastNews, function ($a, $b)
        {
            return $a['timepar'] > $b['timepar'] ? -1 : 1;
        });

        return $lastNews;
    }

    public function renderAttributes()
    {
        $time = strtotime($this->time);
        return
            [
                'title'=>$this->title,
                'time' => date('d.m.Y',$time),
                'timepar' => $time,
                'type' => 'news',
                'id' => $this->id,
                'text' => $this->text,
                'issuer' => ['id' => $this->issuer_id, 'name' => $this->issuer->nickname]
            ];
    }
}