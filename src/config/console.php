<?php

return [
    'id' => 'app-console',
    'basePath' => YII_APP_BASE_PATH,
	'runtimePath' => YII_APP_BASE_PATH.'/runtime/console',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'user' => [
            'class' => 'ant\user\console\controllers\UserController',
        ],
		'module' => [
			'class' => 'ant\moduleManager\console\controllers\DefaultController',
		],
		'core-migrate' => [
			'class' => 'ant\moduleManager\console\controllers\MigrateController',
            'migrationPath' => [
                '@common/migrations/db',
                '@yii/rbac/migrations',
            ],
            'migrationNamespaces' => [
				'ant\moduleManager\migrations\db',
			],
            'migrationTable' => '{{%system_db_migration}}'
		],
		'migrate' => [
			'class' => 'ant\moduleManager\console\controllers\MigrateController',
            'migrationPath' => [
                '@common/migrations/db',
                '@yii/rbac/migrations',
				'@project/migrations/db',
            ],
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
				'ant\moduleManager\migrations\db',
			],
            'migrationTable' => '{{%system_db_migration}}'
		],
		'rbac-migrate' => [
			'class' => 'ant\moduleManager\console\controllers\RbacMigrateController',
            'migrationPath' => [
                '@common/migrations/rbac',
				'@project/migrations/rbac',
            ],
            'migrationTable' => '{{%system_rbac_migration}}',
            'migrationNamespaces' => [
                'ant\moduleManager\migrations\rbac',
			],
            'templateFile' => '@common/rbac/views/migration.php'
		],
        'fixture' => [
			'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'tests\codeception\common\fixtures',
        ],
		'serve' => [
			'class' => 'yii\console\controllers\ServeController',
			'docroot' => './',
		],
		'maintenance' => [
			'class' => 'brussens\maintenance\commands\MaintenanceController',
		],
    ],
    'components' => [
		'maintenanceMode' => [
			'class' => 'brussens\maintenance\MaintenanceMode',
		],
        'frontendCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@frontend/runtime/cache'
        ],
        'backendCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@backend/runtime/cache'
        ],
		'assetManager' => [
			'class' => 'yii\web\AssetManager',
			'basePath' => '@project/frontend/web/assets',
			'baseUrl' => '@frontendUrl/assets',
		],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
			'baseUrl' => Yii::getAlias(env('FRONTEND_URL')),
			'hostInfo' => '',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
];
