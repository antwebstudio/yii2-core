<?php
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_LOCALHOST') or define('YII_LOCALHOST', true);

require_once(YII_PROJECT_BASE_PATH . '/vendor/autoload.php');
require_once(YII_PROJECT_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', YII_PROJECT_BASE_PATH.'/tests');
Yii::setAlias('@baseUrl', 'http://localhost/ant/eventmy');
Yii::setAlias('@frontendUrl', 'http://localhost/ant/eventmy');
Yii::setAlias('@storageUrl', 'http://localhost/ant/eventmy');
Yii::setAlias('@baseUrl', 'http://localhost:8080');
Yii::setAlias('@frontendUrl', 'http://localhost:8080');
Yii::setAlias('@webroot', YII_PROJECT_BASE_PATH.'/frontend/web');
Yii::setAlias('@project', YII_PROJECT_BASE_PATH);

require(__DIR__ . '/env.php');

//require_once __DIR__ .' /alias.php';