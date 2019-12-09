#!/usr/bin/env php
<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_PROJECT_BASE_PATH') or define('YII_PROJECT_BASE_PATH', dirname(dirname(dirname(__DIR__))));
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', YII_PROJECT_BASE_PATH);

require_once __DIR__ . '/../../autoload.php';

//require(__DIR__ . '/bootstrap.php');
\ant\base\Bootstrap::run(\ant\base\Bootstrap::INIT, YII_PROJECT_BASE_PATH, YII_APP_BASE_PATH);

$application = new yii\console\Application([
    'id' => 'yii-console',
    'basePath' => __DIR__ . '/console',
    'controllerNamespace' => 'yii\console\controllers',
]);
if ($vendorPath !== null) {
    $application->setVendorPath($vendorPath);
}
$exitCode = $application->run();
exit($exitCode);



$composerAutoload = [
    __DIR__ . '/../vendor/autoload.php', // in yii2-dev repo
    __DIR__ . '/../../autoload.php', // installed as a composer binary
];
$vendorPath = null;
foreach ($composerAutoload as $autoload) {
    if (file_exists($autoload)) {
        require $autoload;
        $vendorPath = dirname($autoload);
        break;
    }
}


require __DIR__ . '/Yii.php';

$application = new yii\console\Application([
    'id' => 'yii-console',
    'basePath' => __DIR__ . '/console',
    'controllerNamespace' => 'yii\console\controllers',
]);
if ($vendorPath !== null) {
    $application->setVendorPath($vendorPath);
}
$exitCode = $application->run();
exit($exitCode);
