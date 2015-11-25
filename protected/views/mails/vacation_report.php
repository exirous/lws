<?

?>
<html>
Рапорт на отпуск от <a href="http://<?=Yii::app()->params['isIL2'] ? 'bos.' : ''?>luftwaffeschule.ru/#/user/view/<?=$userId?>"><?=$userNickname?></a> с <?=$dateFrom?> по <?=$dateTo?><br>
<?=$reason?>
</html>