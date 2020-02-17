<?php
require(dirname(__DIR__).'/bootstrap.php');

\ant\base\Bootstrap::run(\ant\base\Bootstrap::TEST_FUNCTIONAL, YII_PROJECT_BASE_PATH, YII_APP_BASE_PATH);

\Yii::setAlias('@baseUrl', 'http://localhost');
\Yii::setAlias('@frontendUrl', '@baseUrl/web');
\Yii::setAlias('@storageUrl', '@baseUrl/storage/web');
\Yii::setAlias('@apiUrl', '@baseUrl/web/api');