<?php

return [
	'id' => 'api-application',
    'basePath' => dirname(__DIR__),
	'as authenticator' => [
		'class' => \ant\rbac\ApiGlobalAccessControl::className(),
		'authMethods' => [
			\yii\filters\auth\HttpBasicAuth::className(),
			\yii\filters\auth\HttpBearerAuth::className(),
			\yii\filters\auth\QueryParamAuth::className(),
		],
		'optional' => [
			'v1/login',
			'user/default/login',
			'user/v1/login/index',
			'user/default/request-password-reset',
			'sendBird/webhook/*',
		],
	],
	'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => env('FRONTEND_COOKIE_VALIDATION_KEY'),
        ],
		'response' => [
			'format' =>  \yii\web\Response::FORMAT_JSON
		],
		'user' => [
			'enableAutoLogin' => false,
			'enableSession' => false,
		],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
				'v1/login' => 'user/v1/login/index',
            ],
        ],
	],
];