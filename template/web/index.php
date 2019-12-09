<?php
//ini_set('display_errors', 1); error_reporting(E_ALL);

require(dirname(__DIR__).'/bootstrap.php');
\ant\base\Bootstrap::run(\ant\base\Bootstrap::FRONTEND, YII_PROJECT_BASE_PATH, YII_APP_BASE_PATH);
