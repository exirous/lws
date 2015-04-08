<?php
$base64 = '[a-zA-Z0-9=+\/]+';
$digits = '[0-9\-]+'; //  Digits with a '-' delimiter
$lang = '<lang:en|ru|>/?';

return array(
    'img/groups/<id:[0-9]+>.png' => 'teamSpeak/emptyIcon',
    'c/<cat:[a-z0-9\-\/,]+>'=>'offer/list/category/<cat>',
    'f/<filter:[a-z0-9\-\/,]+>'=>'offer/list/<filter>',
    'id<id:'.$digits.'>' => 'site/redirectById',
    '<c:[a-zA-Z]+>/<a:[a-zA-Z]+>/<id:\d+|_id_>' => '<c>/<a>',
    '<c:[a-zA-Z]+>/<a:[a-zA-Z]+>' => '<c>/<a>',
);
?>