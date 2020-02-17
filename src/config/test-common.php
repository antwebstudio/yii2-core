<?php
return [
	'id' => 'app-test',
	'basePath' => dirname(__DIR__),
	'aliases' => [
		'ant' => YII_APP_BASE_PATH.'/src',
		//'api' => dirname(dirname(__DIR__)).'/src/api',
		'common/config' => YII_APP_BASE_PATH.'/tests/config', // dirname(dirname(__DIR__)).'/vendor/inspirenmy/yii2-core/src/common/config',
		'ant/moduleManager' => YII_APP_BASE_PATH.'/vendor/antweb/yii2-core/src/moduleManager',
		'vendor' => YII_APP_BASE_PATH.'/vendor',
		'@common/migrations' => '@vendor/antweb/yii2-core/src/migrations',
	],
    'components' => [
        'mutex' => [
            'class' => 'yii\mutex\MysqlMutex',
        ],
		'payment' => [
			'class' => 'ant\payment\components\PaymentComponent',
			'paymentGateway' => [
                'ipay88' => [
					'class' => 'ant\payment\components\IPay88PaymentMethod',
					'config' => [
						'merchantCode' => 'M09111',
						'merchantKey' => 'tFgrFE0vUR',
						'sandboxUrl' => ['/sandbox', 'sandbox' => 'ipay88'],
						'sandboxRequeryUrl' => ['/sandbox', 'sandbox' => 'ipay88', 'requery' => 1],
						'requeryNeeded' => false,
					],
					'overrideMethods' => [
						'getPaymentDataForGateway' => function($payable, $paymentGateway) {
							$paymentId = 2;
							
							$returnUrl = \Yii::$app->payment->getReturnUrl($paymentGateway->name);
							$cancelUrl = \Yii::$app->payment->getCancelUrl($paymentGateway->name);

							$cancelUrlParams = $returnUrlParams = \Yii::$app->request->get();
							array_unshift($returnUrlParams, $returnUrl);
							array_unshift($cancelUrlParams, $cancelUrl);
							$backendUrlParams = $returnUrlParams;
							$backendUrlParams['backend']  = 1;
							
							return [
								'amount' => $payable->getDueAmount(),
								'currency' => $payable->getCurrency(),
								'expires_in' =>  time() + 10,
								'card' => [
									'billingName' => 'test', // $payableModel->billedTo->contactName,
									'email' => 'test@example.com', // $payableModel->billedTo->email,
									'number' => '01640612345', // $payableModel->billedTo->contact_number,
								],
								'paymentId' => $paymentId,
								'description' => 'Event Registration Fee',
								'transactionId' => $payable->getReference(),
								// 'card' => $formData,

								'returnUrl' => 'localhost',
								'cancelUrl' => '',
								'backendUrl' => '',
							];
						},
					],
                ],
			],
		],
		'notifier' => [
           'class' => '\tuyakhov\notifications\Notifier',
		   'on '.\tuyakhov\notifications\Notifier::EVENT_AFTER_SEND => function($event) {
		   },
       ],
		'expressionParser' => [
			'class' => 'ant\attribute\components\ExpressionParser',
		],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=test_test',
            'username' => 'root',
            'password' => 'root',
            'tablePrefix' => '',
            'charset' => 'utf8',
        ],
        'moduleManager' => [
            'class' => 'ant\moduleManager\ModuleManager',
			'moduleAutoloadPaths' => [
				'@ant', 
				'@vendor/antweb/yii2-ecommerce/src', 
				'@vendor/antweb/yii2-payment/src', 
				'@vendor/antweb/yii2-cart/src', 
				'@vendor/antweb/yii2-user/src',
				'@vendor/antweb/yii2-cms/src',
			],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => [\ant\rbac\Role::ROLE_GUEST, \ant\rbac\Role::ROLE_USER],
        ],
        'user' => [
			'class' => 'yii\web\User',
            'identityClass' => 'ant\user\models\User',
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
				'@common/migrations/db',
                '@yii/rbac/migrations',
			],
            'migrationTable' => '{{%system_db_migration}}'
		],
		'rbac-migrate' => [
			'class' => 'ant\moduleManager\console\controllers\RbacMigrateController',
            'migrationPath' => [
                '@common/migrations/rbac',
                '@yii/rbac/migrations',
            ],
            'migrationTable' => '{{%system_rbac_migration}}',
            'migrationNamespaces' => [
                'ant\moduleManager\migrations\rbac',
			],
            'templateFile' => '@common/rbac/views/migration.php'
		],
	],
];