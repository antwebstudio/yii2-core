<?php
return \yii\helpers\ArrayHelper::merge(
	require __DIR__ . '/test-common.php',
	require __DIR__ . '/test-web.php'
);