<?php
/**
 * Application config for common unit tests
 */
return yii\helpers\ArrayHelper::merge(
    require(YII_APP_BASE_PATH . '/common/config/main.php'),
    require(YII_APP_BASE_PATH. '/tests/codeception/config/config.php'),
    require(__DIR__ . '/common.php'),
    require(__DIR__ . '/local.php')
);
