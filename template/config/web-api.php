<?php

return \yii\helpers\ArrayHelper::merge(
	require __DIR__ . '/common.php',
	require __DIR__ . '/web.php',
	require __DIR__ . '/api.php'
);