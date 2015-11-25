<?
/**
 * @var User $newUser
 */
?>
<html>
<body>
<h3>Добро пожаловать в школу пилотов LuftWaffeSchule</h3>
<div>
    Что-бы вас приняли, пожалуйста зайдите на наш <a href="http://<?=Yii::app()->params['isIL2'] ? 'bos.' : ''?>luftwaffeschule.ru/teamSpeak/redirect/?id=<?=$newUser->id?>">сервер
        TeamSpeak по адресу luftwaffeschule.ru</a>  для собеседования и ожидайте в приёмной.<br>
    Кто-то из инструкторов к вам обязательно подойдёт.
    <br>
    TeamSpeak можно скачать пройдя по <a href="http://www.teamspeak.com/?page=downloads" target="_blank">этой</a>
    ссылке.
</div>
</body>
</html>