<?php

class NewsController extends Controller
{
    var $layout = "json";

    public function actions()
    {
        Yii::t('forms', 'cate');
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class'     => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page'    => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionLast()
    {
        $content = ['news' => []];
        foreach (News::model()->findAll(['order' => 'id desc', 'limit' => 8]) as $news)
        {
            $content['news'][] = [
                'time'    => $news->time,
                'timepar' => strtotime($news->time),
                'type'    => 'news',
                'id'      => $news->id,
                'text'    => $news->text,
                'issuer'  => ['id' => $news->issuer_id, 'name' => $news->issuer->nickname]
            ];
        }
        foreach (Order::model()->findAll(['order' => 'id desc', 'limit' => 8]) as $order)
        {
            $content['news'][] = [
                'time'    => $order->time,
                'timepar' => strtotime($order->time),
                'type'    => 'order',
                'id'      => $order->id,
                'text'    => $order->text,
                'issuer'  => ['id' => $order->issuer_id, 'name' => $order->issuer->nickname]
            ];
        }

        usort($content['news'], function ($a, $b)
        {
            return $a['timepar'] > $b['timepar'] ? -1 : 1;
        });

        $this->render('//common/json', compact('content'));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error)
        {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }
}