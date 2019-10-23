<?php

require_once(YII_PROJECT_BASE_PATH . '/vendor/autoload.php');
require_once(__DIR__ . '/common.php');
//require(YII_APP_BASE_PATH . '/frontend/config/bootstrap.php');

$config = \ant\base\ConfigBuilder::load(\ant\base\ConfigBuilder::BACKEND);

$application = new yii\web\Application($config);
$application->run();