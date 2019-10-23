<?php

return \yii\helpers\ArrayHelper::merge(
	require __DIR__ . '/common.php',
	require __DIR__ . '/console.php'
);