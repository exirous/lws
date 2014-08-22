<?
/**
 * @var User $user
 */
?>
<html>
<body>
<h3>Заявка в LuftWaffeSchule</h3>
<div>
    Уважаемый <?=$user->firstname?>!
    К сожелению мы не можем вас в данный момент принять, причина отклонения заявки такова:<br>
    <b><?=$reason?></b><br>
    С уважением, Администрация LuftWaffeSchule
</div>
</body>
</html>