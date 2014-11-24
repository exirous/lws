<?
/**
 * @var User $user
 */
?>
<html>
<body>
<h3>Исключение из LuftWaffeSchule</h3>
<div>
    Уважаемый <?=$user->firstname?>!<br>
    Вы были исключены из школы пилотов по причине:<br>
    <b><?=$reason?></b><br>
    С уважением, Администрация LuftWaffeSchule
</div>
</body>
</html>