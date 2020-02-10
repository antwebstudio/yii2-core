<?php

defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(YII_PROJECT_BASE_PATH)));

require(YII_PROJECT_BASE_PATH . '/vendor/autoload.php');
//require(__DIR__ . '/common.php');
require_once(YII_PROJECT_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php');

//$config = \ant\base\ConfigBuilder::load(\ant\base\ConfigBuilder::CONSOLE);

/*$application = new yii\console\Application($config);
$exitCode = $application->runAction('init');
exit($exitCode);*/