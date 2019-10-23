<?php
return \yii\helpers\ArrayHelper::merge(
	require dirname(__DIR__).'/config/common.php',
	require dirname(__DIR__).'/config/web.php'
);