<?php

$config = [
    'id' => 'app-backend',
    'basePath' => YII_APP_BASE_PATH.'/backend',
	'runtimePath' => YII_APP_BASE_PATH.'/runtime/backend',
    'bootstrap' => ['log'],
    //'layout' => 'default',
    'controllerNamespace' => 'backend\controllers',
    'modules' => [
		'moduleManager' => [
			'class' => 'backend\modules\moduleManager\Module',
		],
        'cms' => [
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
        ],
    ],
    'components' => [
        'moduleManager' => [
            'class' => 'ant\moduleManager\components\ModuleManager',
			'moduleAutoloadPaths' => ['@backend/modules', 
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-ecommerce/src/backend/modules', 
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-user/src/backend/modules',
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-core/src/backend/modules',
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-cms/src/backend/modules',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-importer/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-dashboard/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-affiliate/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-event/src/backend',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-cart/src/backend/modules',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-payment/src',
			],
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
                'asset' => '\backend\themes\adminlte\assets\ThemeAsset',
                'pathMap' => [
					'@app/views' => [
						'@vendor/inspirenmy/yii2-core/src/backend/themes/adminlte/views',
						'@vendor/inspirenmy/yii2-core/src/backend/views',
					],
                    '@backend/modules' => '@vendor/inspirenmy/yii2-ecommerce/src/backend/modules',
					
					'@vendor/inspirenmy/yii2-core/src/backend/modules' => '@project/backend/views/modules',
					'@vendor/inspirenmy/yii2-ecommerce/src/backend/modules' => '@project/backend/views/modules',
					
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