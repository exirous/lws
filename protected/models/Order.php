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
            'issuer' => ['id' => $this->issuer_id, 'name' => $this->issuer->nickname]
        ];
    }

    public static function issueOrder($data)
    {
        if (!isset($data['time']))
            $date = gmdate("Y-m-d H:i:s");
        else
            $date = gmdate("Y-m-d H:i:s", strtotime($data['time']));

        $order = new Order();
        $order->text = $data['complete'];
        $order->issuer_id = Yii::app()->user->model->id;
        $order->time = $date;
        if (!$order->save())
            throw new Exception('1 ' . $order->getErrorsString());

        $eventText = trim(isset($data['event']) ? $data['event'] . ' ' : '');
        $customText = trim(isset($data['customText']) ? ' ' . $data['customText'] : '');

        foreach ($data['pilots'] as $pilotData)
        {
            $pilot = User::model()->findByPk($pilotData['id']);
            if (!$pilot)
                throw new Exception('Пользователь не найден');

            $needSave = false;

            if (isset($pilotData['instructor']) && $pilot->instructor_id != $pilotData['instructor'])
            {
                $rank = Rank::model()->findByPk($pilotData['instructor']);
                if (!$rank)
                    throw new Exception('Звание не найдено');
                $event = new UserEvent();
                $event->user_id = $pilot->id;
                $event->order_id = $order->id;
                $event->date = $date;
                $event->text = $eventText . 'Присвоена должность <a rank="' . $rank->id . '">' . $rank->name . '</a>';
                if (!$event->save())
                    throw new Exception('2 ' . $event->getErrorsString());
                $pilot->instructor_id = $rank->id;
                $needSave = true;
            }

            if (isset($pilotData['rank']) && $pilot->rank_id != $pilotData['rank'])
            {
                $rank = Rank::model()->findByPk($pilotData['rank']);
                if (!$rank)
                    throw new Exception('Звание не найдено');
                $event = new UserEvent();
                $event->user_id = $pilot->id;
                $event->order_id = $order->id;
                $event->date = $date;
                $text = 'Присвоено звание ';
                if ($pilot->rank->order > $rank->order)
                    $text = 'Понижен до ';
                if ($rank->id == 11 || $rank->id == 12)
                    $text = 'Переведён на ';
                if ($rank->id == 7)
                    $text = 'Принят на ';
                $event->text = $eventText . $text . '<a rank="' . $rank->id . '">' . $rank->name . '</a>';
                if (!$event->save())
                    throw new Exception('3 ' . $event->getErrorsString());
                $pilot->rank_id = $rank->id;
                if ($rank->order > 6)
                    $pilot->is_clanner = false;
                $needSave = true;
            }

            if ($customText && $customText != '')
            {
                $event = new UserEvent();
                $event->user_id = $pilot->id;
                $event->order_id = $order->id;
                $event->date = $date;
                $event->text = $eventText .' '. $customText;
                if (!$event->save())
                    throw new Exception('5 ' . $event->getErrorsString());
            }

            if (isset($pilotData['awards']))
            {
                foreach ($pilotData['awards'] as $awardId)
                {
                    $award = Award::model()->findByPk($awardId);
                    if (!$award)
                        throw new Exception('Медаль не найдена');

                    $event = new UserEvent();
                    $event->user_id = $pilot->id;
                    $event->order_id = $order->id;
                    $event->date = $date;
                    $event->text = $eventText . 'Награждён <a award="' . $award->id . '">' . $award->name . '</a>';
                    if (!$event->save())
                        throw new Exception('4 ' . $event->getErrorsString());
                    $userAward = new UserAward();
                    $userAward->user_id = $pilot->id;
                    $userAward->event_id = $event->id;
                    $userAward->award_id = $award->id;
                    if (!$userAward->save())
                        throw new Exception('5 ' . $userAward->getErrorsString());
                }
            }

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