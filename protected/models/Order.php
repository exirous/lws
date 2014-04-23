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
        $date = gmdate("Y-m-d H:i:s");
        $order = new Order();
        $order->text = $data['complete'];
        $order->issuer_id = Yii::app()->user->model->id;
        $order->time = $date;
        if (!$order->save())
            throw new Exception($order->getErrorsString());

        $eventText = (isset($data['event']) ? $data['event'] . ' ' : '');

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
                $event->text = $eventText . 'Присвоена степень <a rank="' . $rank->id . '">' . $rank->name . '</a> ';
                if (!$event->save())
                    throw new Exception($event->getErrorsString());
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
                    $text = 'Зачислен на ';
                $event->text = $eventText . $text . '<a rank="' . $rank->id . '">' . $rank->name . '</a> ';
                if (!$event->save())
                    throw new Exception($event->getErrorsString());
                $pilot->rank_id = $rank->id;
                $needSave = true;
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
                    $event->text = $eventText . 'Награждён <a award="' . $award->id . '">' . $award->name . '</a> ';
                    if (!$event->save())
                        throw new Exception($event->getErrorsString());
                    $userAward = new UserAward();
                    $userAward->user_id = $pilot->id;
                    $userAward->event_id = $event->id;
                    $userAward->award_id = $award->id;
                    if (!$userAward->save())
                        throw new Exception($userAward->getErrorsString());
                }
            }

            if ($needSave && !$pilot->save())
                throw new Exception($pilot->getErrorsString());

            $pilot->syncWithTeamSpeak();
        }
        return $order->id;
    }
}