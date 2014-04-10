<?php

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

$path = dirname(__FILE__) . '/protected';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once('framework/yii.php');
require_once('protected/components/AWebApplication.php');
//Проверка коммитов 123

$app = Yii::createApplication('AWebApplication', 'config/main.php');
$app->run();
