<?php

Yii::import('application.models._base.BaseOrder');

/**
 * @method Order find
 * @method Order[] findAll
 * @method Order findByPk
 * @method Order[] findAllByPk
 * @method Order findByAttributes
 * @method Order[] findAllByAttributes
 * @method Order findBySql
 * @method Order[] findAllBySql
 * @method Order cache
 * @method Order resetScope
 * @method Order with
 * @method Order together
 * @method Order populateRecord
 * @method Order scopeLimit
 * @method Order scopeOffset
 * @method Order scopeOrder
 * @method Order scopeAllColumns
 * @method Order scopeSelect
 * @method Order byName
 */
class Order extends BaseOrder
{
    /**
     * @static
     * @param string $className
     * @return Order
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

    public function renderAttributes()
    {
        $time = strtotime($this->time);
        return [
            'title' => 'Приказ №' . $this->id,
            'time' => date('d.m.Y', $time),
            'timepar' => $time,
            'type' => 'order',
            'id' => $this->id,
            'text' => $this->text,
            'issuer' => $this->issuer->getListAttributes()
        ];
    }

    public static function getLast()
    {
        $lastNews = [];
        foreach (Order::model()->findAll(['order' => 'time desc', 'limit' => 8]) as $news)
            $lastNews[] = $news->renderAttributes();

        /*usort($lastNews, function ($a, $b)
        {
            return $a['timepar'] > $b['timepar'] ? -1 : 1;
        });*/

        return $lastNews;
    }


    public static function issueOrder($data)
    {
        if (!isset($data['time']))
            $date = date("Y-m-d H:i:s");
        else
            $date = date("Y-m-d H:i:s", strtotime($data['time']));

        $order = new Order();
        $order->text = $data['complete'];
        $order->issuer_id = Yii::app()->user->model->id;
        $order->time = $date;
        if (!$order->save())
            throw new Exception('1 ' . $order->getErrorsString());

        $data['event'] = isset($data['event']) ? $data['event'] : '';
        $data['customText'] = isset($data['customText']) ? $data['customText'] : '';

        $eventText = trim($data['event']) ? trim($data['event']) . ' ' : '';
        $customText = trim($data['customText']) ? ' ' . trim($data['customText']) : '';

        foreach ($data['pilots'] as $pilotData)
        {
            $pilot = User::model()->findByPk($pilotData['id']);
            if (!$pilot)
                throw new Exception('Пользователь не найден');

            $needSave = false;
            $eventRankText = $eventInstructorText = $eventAwardText = '';
            $event = new UserEvent();
            $event->user_id = $pilot->id;
            $event->order_id = $order->id;
            $event->date = $date;

            if (isset($pilotData['rank']) && $pilot->rank_id != $pilotData['rank'])
            {
                $rank = Rank::model()->findByPk($pilotData['rank']);
                if (!$rank)
                    throw new Exception('Звание не найдено');

                $text = 'Присвоено звание ';
                if ($pilot->rank->order > $rank->order)
                    $text = 'Понижен до ';
                if ($rank->id == 11 || $rank->id == 12)
                    $text = 'Переведён на ';
                if ($rank->id == 7)
                    $text = 'Принят на ';

                if ($eventText)
                    $text = strtolower($text);

                $eventRankText = $text . '<a rank="' . $rank->id . '">' . $rank->name . '</a>';
                $pilot->rank_id = $rank->id;
                if ($rank->order > 5)
                {
                    $pilot->is_clanner = false;
                    if ($pilot->rank->order < 5)
                    $pilotData['awards'][] = '41';
                }
                $needSave = true;
            }

            if (isset($pilotData['instructor']) && $pilot->instructor_id != $pilotData['instructor'])
            {
                $rank = Rank::model()->findByPk($pilotData['instructor']);
                if (!$rank)
                    throw new Exception('Звание не найдено');

                $eventInstructorText = (($eventText || $eventRankText) ? 'присвоена' : 'Присвоена').' должность <a rank="' . $rank->id . '">' . $rank->name . '</a>';
                $pilot->instructor_id = $rank->id;
                $needSave = true;
            }

            if (isset($pilotData['awards']))
            {
                $eventAwardText = [];
                foreach ($pilotData['awards'] as $awardId)
                {
                    $award = Award::model()->findByPk($awardId);
                    if (!$award)
                        throw new Exception('Медаль не найдена');

                    $eventAwardText[] = '<a award="' . $award->id . '">' . $award->sub_name . '</a>';
                    $userAward = new UserAward();
                    $userAward->user_id = $pilot->id;
                    //$userAward->event_id = $event->id;
                    $userAward->award_id = $award->id;
                    if (!$userAward->save())
                        throw new Exception('5 ' . $userAward->getErrorsString());
                }
                $eventAwardText = (($eventText || $eventRankText || $eventInstructorText) ? 'награждён ' : 'Награждён ').implode(', ',$eventAwardText);
            }

            $event->text = $eventText.$eventRankText.
                ($eventRankText && $eventInstructorText ? ' и ' : '').$eventInstructorText.
                ((($eventRankText || $eventInstructorText) && $eventAwardText) ? ' и ' : '').$eventAwardText.
                ((($eventRankText || $eventInstructorText || $eventAwardText) && $customText) ? ' и ' : '').$customText;

            if (isset($data['onlyatest']))
                die($event->text);

            if (trim($event->text))
                if (!$event->save())
                    throw new Exception('3 ' . $event->getErrorsString());

            if ($needSave && !$pilot->save())
                throw new Exception('6 ' . $pilot->getErrorsString());

            $pilot->syncWithTeamSpeak();
        }
        if (strtotime($order->time) > (time() - 3600 * 48))
            $order->postToTeamSpeak();
        return $order->id;
    }

    public function postToTeamSpeak()
    {
        $fullmessage = str_replace(["\n", "\r"], '', $this->text);
        $fullmessage = str_replace('</p>', "\n", $fullmessage);
        $pos = stripos($fullmessage, '<p>');
        $event = (substr($fullmessage, 0, $pos));
        $fullmessage = (substr($fullmessage, $pos + 3));
        $event = '[b]Приказ №' . $this->id . ':[/b] ' . $event . "\n";
        $fullmessage = str_replace('</a>', '[/COLOR]', $fullmessage);
        $fullmessage = preg_replace('/(\<a rank\="([0-9]+)")\>/', '[COLOR=blue]', $fullmessage);
        $fullmessage = preg_replace('/(\<a pilot\="([0-9]+)")\>/', '[COLOR=red]', $fullmessage);
        $fullmessage = preg_replace('/(\<a award\="([0-9]+)")\>/', '[COLOR=darkgreen]', $fullmessage);
        $i = 1;
        do
        {
            $nicknameInUse = false;
            try
            {
                Yii::app()->ts->setName('Отдел кадров №' . $i);
                Yii::app()->ts->ts3Server->message($event . $fullmessage);
            } catch (Exception $e)
            {
                if ($e->getMessage() == 'nickname is already in use')
                    $nicknameInUse = true;
            }
            $i++;
        } while ($nicknameInUse && ($i < 20));
    }
}