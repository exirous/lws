<?
/**
 * @var User $user
 */
?>
<html>
<body>
<div>
    <?=$user->firstname?> (<?=$user->nickname?>), <?=$user->email?> был отчислен пользователем <?=Yii::app()->user->model->nickname?>
    по причине :<br>
    <b><?=$reason?></b><br>
</div>
</body>
</html>