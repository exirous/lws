<?php

class DailyCommand extends CConsoleCommand
{

    public function actionCheckInactiveAndSendWarnings()
    {
        if(isset(Yii::app()->controller))
            $controller = Yii::app()->controller;
        else
            $controller = new CController('SendWarnings');

        $viewPath = Yii::getPathOfAlias('application.views.mails.user_inactive_warning').'.php';
        $users = User::model()->scopeInactive()->scopeEnabled()->scopeNeedWarning()->findAll();
        foreach ($users as $user) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $user->last_warning_time = date("Y-m-d H:i:s");
                $user->is_defector = 1;
                $user->save();
                Mailer::send($user->email, 'Предупреждение об отчислении', $controller->renderInternal($viewPath, compact('user'), true));
                Mailer::send('luftwaffeschule@gmail.com', 'Вероятная дезертация', Yii::app()->controller->renderPartial('//mails/user_defector_notify', ['user' => $user], true));
                $transaction->commit();
            }
            catch(Exception $e)
            {
                $transaction->rollback();
                echo $e->getMessage()."\n";
            }
        }
    }

}

