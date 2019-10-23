<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_LOCALHOST') or define('YII_LOCALHOST', true);

defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(YII_PROJECT_BASE_PATH)));

require_once(YII_PROJECT_BASE_PATH . '/vendor/autoload.php');
require_once(__DIR__ . '/common.php');

Yii::setAlias('@tests', dirname(dirname(__DIR__)));

$config = \ant\base\ConfigBuilder::load(\ant\base\ConfigBuilder::TEST);

$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);

