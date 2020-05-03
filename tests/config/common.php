<?php

return [
	'id' => 'app-test',
	'basePath' => dirname(__DIR__),
	'timezone' => 'Asia/Kuala_Lumpur',
	'aliases' => [
		'api' => dirname(dirname(__DIR__)).'/src/api',
		'common/config' => __DIR__, // dirname(dirname(__DIR__)).'/vendor/inspirenmy/yii2-core/src/common/config',
		'common/modules/moduleManager' => dirname(dirname(__DIR__)).'/vendor/inspirenmy/yii2-core/src/common/modules/moduleManager',
		'vendor' => dirname(dirname(__DIR__)).'/vendor',
		// Should be comment for yii2-core
		//'@common/migrations' => '@vendor/inspirenmy/yii2-core/src/common/migrations',
		'@common/rbac' => '@vendor/inspirenmy/yii2-core/src/common/rbac',
		'@ant' => dirname(dirname(__DIR__)).'/src',
	],
	'bootstrap' => ['gii'],
	'modules' => [
		'gii' => [
			'class' => 'yii\gii\Module',
		],
	],
    'components' => [
        'formatter' => [
            'defaultTimeZone' => 'Asia/Kuala_Lumpur', // Must match to server mysql timezone or else will show incorrect date time retrieve from mysql
            'dateFormat' => 'dd-MM-yyyy',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            //'currencyCode' => 'MYR',
            'numberFormatterSymbols' => [
                NumberFormatter::CURRENCY_SYMBOL => 'RM ',
            ]
       ],
        'i18n' => [
            'translations' => [
                '*'=> [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath'=>'@common/messages',
                ],
            ],
        ],
		'urlManagerFrontEnd' => [
			'class' => 'yii\web\UrlManager',
		],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=test_test',
            'username' => 'root',
            'password' => 'root',
            'tablePrefix' => '',
            'charset' => 'utf8',
			'on afterOpen' => function($event) { 
				$now = new \DateTime();
				$mins = $now->getOffset() / 60;
				$sgn = ($mins < 0 ? -1 : 1);
				$mins = abs($mins);
				$hrs = floor($mins / 60);
				$mins -= $hrs * 60;
				$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
				$event->sender->createCommand("SET time_zone='$offset';")->execute(); 
			},
        ],
        /*'moduleManager' => [
            'class' => 'ant\moduleManager\ModuleManager',
			'moduleAutoloadPaths' => [
				'@ant', 
				'@vendor/inspirenmy/yii2-ecommerce/src/common/modules', 
				'@vendor/inspirenmy/yii2-user/src/common/modules',
				'@vendor/inspirenmy/yii2-core/src/common/modules',
			],
        ],*/
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user' => [
			'class' => 'yii\web\User',
            'identityClass' => 'ant\user\models\User',
        ],
        'mailer' => [
            'syncMailer' => [
                'useFileTransport' => true,
                'viewPath' => '@tests/mail',
            ],
        ],
	],
	'controllerMap' => [
		'module' => [
			'class' => 'ant\moduleManager\console\controllers\DefaultController',
		],
		'migrate' => [
			'class' => 'ant\moduleManager\console\controllers\MigrateController',
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
				'ant\moduleManager\migrations\db',
			],
			'migrationPath' => [
				'@common/migrations/db', // Need to first one so that yii migrate/create will use this path
                '@yii/rbac/migrations',
				'@tests/migrations/db',
			],
            'migrationTable' => '{{%system_db_migration}}'
		],
		'rbac-migrate' => [
			'class' => 'ant\moduleManager\console\controllers\RbacMigrateController',
            'migrationPath' => [
                '@common/migrations/rbac',
            ],
            'migrationTable' => '{{%system_rbac_migration}}',
            'migrationNamespaces' => [
                'ant\moduleManager\migrations\rbac',
			],
            'templateFile' => '@common/rbac/views/migration.php'
		],
	],
];