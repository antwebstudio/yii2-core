<?php

$config = [
    'id' => 'app-frontend',
    'basePath' => YII_APP_BASE_PATH.'/frontend',
	'runtimePath' => YII_APP_BASE_PATH.'/runtime/frontend',
	'defaultRoute' => env('DEFAULT_ROUTE', 'site/index'),
	'bootstrap' => ['maintenanceMode'],
    'controllerNamespace' => 'frontend\controllers',
	'layout' => 'default',
    'modules' => [
		'moduleManager' => [
			'class' => 'frontend\modules\moduleManager\Module',
		],
		'sandbox' => [
			'class' => 'frontend\modules\sandbox\Module',
		],
        /*'ecommerce' => ['class' => 'frontend\modules\ecommerce\Module'],
        'cart' => ['class' => 'frontend\modules\cart\Module'],
        'user' => [
        	'class' => 'frontend\modules\user\Module',
        ],*/
                
    ],
    'components' => [
        'moduleManager' => [
            'class' => 'ant\moduleManager\components\ModuleManager',
			'moduleAutoloadPaths' => ['@frontend/modules', 
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-ecommerce/src/frontend/modules', 
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-user/src/frontend/modules',
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-user/src',
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-core/src/frontend/modules',
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-cms/src/frontend/modules',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-importer/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-dashboard/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-affiliate/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-event/src/frontend',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-cart/src/frontend/modules',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-payment/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-booking/src',
			],
        ],
		'sandbox' => [
			'class' => 'frontend\modules\sandbox\components\SandboxManager',
			'gateway' => [
				'ipay88' => [
					'class' => 'frontend\modules\sandbox\gateway\ipay88\Sandbox',
					'merchantCode' => 'sandbox',
					'merchantKey' => 'inspiren',
					'receiver' => [
						'default' => [
							'class' => '\frontend\modules\sandbox\gateway\ipay88\Receiver',
						],
						'requery' => [
							'class' => '\frontend\modules\sandbox\gateway\ipay88\RequeryReceiver',
							//'reachedDayLimit' => true,
						],
					],
				],
			],
		],
        'menu' => [
            'class' => 'ant\components\MenuManager',
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => env('FRONTEND_COOKIE_VALIDATION_KEY'),
        ],
        'user' => [
            //'class' => 'ant\components\yii\web\User',
			'loginUrl' => ['/user/signin/login'],
			//'logoutUrl' => ['/user/signin/logout'],
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-eventmy', 'httpOnly' => true],
        ],
		// Cannot move to common config or else console config will have error.
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
			'rules' => [
				['pattern' => 'sitemap', 'route' => 'sitemap/default/index', 'suffix' => '.xml'],
			],
        ],
		'maintenanceMode' => [
			'class' => 'brussens\maintenance\MaintenanceMode', // Component class namespace
			'title' => 'Under Maintenance', // Page title
			'enabled' => env('MAINTENANCE'), // Mode status
			'route' => 'maintenance/index', // Route to action
			'message' => 'Sorry, this site is currently under maintenance.', // Show message
			'users' => [
				'BrusSENS', // Allowed user names
			],
			'roles' => [ // Allowed roles
				'admin', 'developer'
			],
			'ips' => [ // Allowed IP addresses
				'127.0.0.1',
			],
			'urls' => [ // Allowed URLs
				'site/login',
				'user/signin/login',
			],
			'layoutPath' => '@app/views/layouts/main', // Layout path
			'viewPath' => '@app/views/maintenance', // View path
			'usernameAttribute' => 'username', // User name attribute name
			'statusCode' => 503, // HTTP Status Code
			'retryAfter' => 120 // Retry-After header (or Wed, 21 Oct 2015 07:28:00 GMT for example)
		],
		'view' => [
            'theme' => [
                'basePath' => '@app/themes/'.env('THEME', 'default'),
                'baseUrl' => '@web/themes/'.env('THEME', 'default'),
                'pathMap' => [
                    '@app/views' => [
						'@project/themes/'.env('THEME', 'default').'/views',
						'@app/themes/'.env('THEME', 'default').'/views',
						'@vendor/inspirenmy/yii2-core/src/frontend/views',
					],
                    '@ant' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
                    '@app/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					
					env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-ecommerce/src/common/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-ecommerce/src/frontend/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-user/src/frontend/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-core/src/frontend/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-cms/src/frontend/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-payment/src/common/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-payment/src/frontend/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-cart/src/common/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-cart/src/frontend/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-subscription/src/common/modules' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-subscription/src' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-payment/src' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-booking/src' => [
						'@project/themes/'.env('THEME', 'default').'/views/modules',
						'@app/themes/'.env('THEME', 'default').'/views/modules',
					],
					
					// Email
					
					env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-user/src/user/mails' => [
						'@project/mails/user',
					],
					env('PACKAGES_PATH', '@vendor/antweb').'/yii2-payment/src/payment/mails' => [
						'@project/mails/payment',
					],
                ],
            ],
		],
    ],
    'as beforeRequest' => [
        'class' => 'ant\rbac\GlobalAccessControl',
		'extraRules' => [
			[
				'controllers' => ['site'],
				'allow' => true,
			],
			[
				'controllers' => ['sitemap/*'],
				'allow' => true,
			],
		],
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