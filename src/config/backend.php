<?php

$config = [
    'id' => 'app-backend',
    'basePath' => YII_APP_BASE_PATH,
	'runtimePath' => YII_APP_BASE_PATH.'/runtime/backend',
    'bootstrap' => ['log'],
    //'layout' => 'default',
    'controllerNamespace' => 'ant\backend\controllers',
    'modules' => [
		'moduleManager' => [
			'class' => 'ant\moduleManager\backend\Module',
		],
        /*'cms' => [
            'class' => 'backend\modules\cms\Module',
        ],
        'event' => [
            'class' => 'backend\modules\event\Module',
        ],
        'user' => [
            'class' => 'backend\modules\user\Module',
        ],
        'payment' => [
            'class' => 'backend\modules\payment\Module',
        ],*/
    ],
    'components' => [
        'moduleManager' => [
            'class' => 'ant\moduleManager\ModuleManager',
        ],
        'menu' => [
            'class' => 'ant\components\MenuManager',
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => env('BACKEND_COOKIE_VALIDATION_KEY'),
        ],
        'user' => [
            'loginUrl' => ['site/login'],
            'identityClass' => 'ant\user\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-eventmy', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            //'name' => 'advanced-backend',
            'name' => env('PROJECT_ID', 'app').'-login',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
			],
        ],
        'view' => [
            'theme' => [
                'class' => 'ant\components\Theme',
                'skin' => 'skin-black',
                'asset' => '\ant\themes\\'.env('BACKEND_THEME', 'adminlte').'\assets\ThemeAsset',
                'pathMap' => [
					'@vendor/antweb/yii2-ecommerce/src/ecommerce/backend/views' => '@project/themes/backend/views/ecommerce',
					'@app/views' => [
						'@vendor/antweb/yii2-web/src/themes/'.env('BACKEND_THEME', 'adminlte').'/views',
						'@vendor/antweb/yii2-web/src/backend/views',
						'@vendor/antweb/yii2-core/src/views',
					],
                    '@backend/modules' => '@vendor/antweb/yii2-ecommerce/src/backend/modules',
					
					'@vendor/antweb/yii2-core/src/backend/modules' => '@project/themes/views/backend/views/modules',
					
					// Email
					'@ant/order/mails' => '@project/mail',
					//'@ant/order/mails' => '@project/mails/order',
					
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-payment/src/payment/mails' => [
						'@project/mails/payment',
					],
					
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-ecommerce/src/order/mails' => [
						'@project/mails/order',
					],
                ],
            ],
        ],
		// Cannot move to common config or else console config will have error.
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'as beforeRequest' => [
        'class' => 'ant\rbac\GlobalAccessControl',
    ],
	'params' => [
		'bsVersion' => 4,
	],
];

// Cannot put in common as this will cause console error - console don't have request->getIp
if (!YII_ENV_TEST && YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		'allowedIPs' => ['*'],
	];
}
return $config;