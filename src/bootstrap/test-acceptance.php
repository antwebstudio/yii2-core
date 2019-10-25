<?php
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_LOCALHOST') or define('YII_LOCALHOST', true);

require_once(YII_PROJECT_BASE_PATH . '/vendor/autoload.php');
require_once(YII_PROJECT_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', YII_PROJECT_BASE_PATH.'/tests');
require(__DIR__ . '/env.php');

//require_once __DIR__ .' /alias.php';