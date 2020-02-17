<?php

return \yii\helpers\ArrayHelper::merge(
	require __DIR__ . '/common.php',
	require __DIR__ . '/web.php',
	require __DIR__ . '/frontend.php',
	[
		'components' => [
			'request' => [
			  'enableCsrfValidation' => false,
			],
			'assetManager' => [
				'basePath' => '@tests/assets',
				'linkAssets' => false,
			],
			'mailer' => [
                'viewPath' => '@project/mails',
			],
		],
	]
);