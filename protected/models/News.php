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

    public function rules()
    {
        $defaults = [
            ['time',
                'default',
                'value' => gmdate("Y-m-d H:i:s")
            ]
        ];
        return CMap::mergeArray($defaults, parent::rules());
    }


    public static function getLast()
    {
        $lastNews = [];
        foreach (News::model()->findAll(['order' => 'time desc', 'limit' => 8]) as $news)
            $lastNews[] = $news->renderAttributes();

        /*usort($lastNews, function ($a, $b)
        {
            return $a['timepar'] > $b['timepar'] ? -1 : 1;
        });*/

        return $lastNews;
    }

    public static function add($title, $text)
    {
        if (Yii::app()->user->isGuest || Yii::app()->user->model->rank->order < 6)
            throw new Exception('Вы пока не можете создавать новости!');
        $news = new News();
        $news->text = $text;
        $news->title = $title;
        $news->issuer_id = Yii::app()->user->model->id;
        if (!$news->save())
            throw new Exception($news->getErrorsString());
        $news->postToTeamSpeak();
        return $news->id;
    }

    public function renderAttributes()
    {
        $time = strtotime($this->time);
        return
            [
                'title' => $this->title,
                'time' => date('d.m.Y', $time),
                'timepar' => $time,
                'type' => 'news',
                'id' => $this->id,
                'text' => $this->text,
                'issuer' => ['id' => $this->issuer_id, 'name' => $this->issuer->nickname]
            ];
    }

    public function postToTeamSpeak()
    {
        $fullmessage = '[b]Свежая новость:[/b] ' . str_replace(["\n", "\r"], '', $this->title) . "\n".str_replace(["\n", "\r"], '', $this->text);
        $i = 1;
        do
        {
            $nicknameInUse = false;
            try
            {
                Yii::app()->ts->setName('Отдел вещания №' . $i);
                Yii::app()->ts->ts3Server->message($fullmessage);
            } catch (Exception $e)
            {
                if ($e->getMessage() == 'nickname is already in use')
                    $nicknameInUse = true;
            }
            $i++;
        } while ($nicknameInUse && ($i < 20));
    }

}