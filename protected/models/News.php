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


    public static function getLast($page, $perPage)
    {
        require_once Yii::app()->basePath . "/vendors/jbbcode/Parser.php";
        $parser = new JBBCode\Parser();
        $parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());

        $lastNews = [];
        $from = $perPage * ($page-1);
        foreach (News::model()->with('issuer')->findAll(['condition'=>(Yii::app()->user->isGuest ? 'only_for_registered=0' : '') ,'order' => 'time desc', 'limit' => $perPage, 'offset' => $from]) as $news)
        {
            $newsAttributes = $news->renderAttributes();
            $parser->parse($newsAttributes['text']);
            $newsAttributes['text'] = nl2br($parser->getAsHTML());
            $lastNews[] = $newsAttributes;
        }


        $count =  Yii::app()->db->createCommand('SELECT COUNT(*) FROM `news`'.(Yii::app()->user->isGuest ? ' WHERE only_for_registered=0' : ''))->queryScalar();

        $news = ["records"=>$lastNews,'count'=>$count];
        return $news;
    }

    public static function add($title, $text, $onlyForRegistered)
    {
        if (Yii::app()->user->isGuest || !Yii::app()->user->model->canMakeNews())
            throw new Exception('Вы пока не можете создавать новости!');
        $news = new News();
        $news->text = $text;
        $news->title = $title;
        $news->only_for_registered = $onlyForRegistered ? 1 : 0;
        $news->issuer_id = Yii::app()->user->model->id;
        if (!$news->save())
            throw new Exception($news->getErrorsString());
        $news->postToTeamSpeak();
        return $news;
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
                'issuer' => $this->issuer->getListAttributes()
            ];
    }

    public function editAttributes()
    {
        $time = strtotime($this->time);
        return
            [
                'title' => $this->title,
                'time' => date('d.m.Y', $time),
                'id' => $this->id,
                'text' => $this->text,
                'onlyRegistered' => $this->only_for_registered,
                'issuer' => $this->issuer->getListAttributes()
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