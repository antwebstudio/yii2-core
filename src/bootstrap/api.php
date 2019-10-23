<?php
require_once(__DIR__ . '/common.php');

$config = \ant\base\ConfigBuilder::load(\ant\base\ConfigBuilder::API);

$application = new yii\web\Application($config);
$application->run();