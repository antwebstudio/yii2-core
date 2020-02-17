<?php
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_LOCALHOST') or define('YII_LOCALHOST', true);

require_once(YII_PROJECT_BASE_PATH . '/vendor/autoload.php');
require_once(YII_PROJECT_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', YII_PROJECT_BASE_PATH.'/tests');
Yii::setAlias('@project', YII_PROJECT_BASE_PATH);
Yii::setAlias('@frontend', YII_PROJECT_BASE_PATH);
Yii::setAlias('@common', YII_APP_BASE_PATH.'/common');
Yii::setAlias('@webroot', YII_PROJECT_BASE_PATH);
//throw new \Exception(Yii::getAlias('@webroot'));

/*

Yii::setAlias('@tests', YII_PROJECT_BASE_PATH.'/tests');
Yii::setAlias('@webroot', YII_PROJECT_BASE_PATH);
Yii::setAlias('@project', YII_PROJECT_BASE_PATH);
Yii::setAlias('@frontend', YII_PROJECT_BASE_PATH);
Yii::setAlias('@common', YII_APP_BASE_PATH.'/common');
*/

//$_SERVER['SCRIPT_FILENAME'] = FRONTEND_ENTRY_FILE;
//$_SERVER['SCRIPT_NAME'] = FRONTEND_ENTRY_URL;
//$_SERVER['SERVER_NAME'] =  parse_url($config['test_entry_url'], PHP_URL_HOST);
//$_SERVER['SERVER_PORT'] =  parse_url($config['test_entry_url'], PHP_URL_PORT) ?: '80';
//$_SERVER['REMOTE_ADDR'] =  isset($config['user_ip']) ? $config['user_ip'] : '::1';

require(__DIR__ . '/env.php');

//require_once __DIR__ .' /alias.php';