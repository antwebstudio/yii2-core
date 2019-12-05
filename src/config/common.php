<?php
$config = [
    'name' => env('APPLICATION_NAME', 'My Application'),
	'timezone' => 'Asia/Kuala_Lumpur', // Needed as some server php default timezone set to UTC
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
		'@ant' => YII_APP_BASE_PATH,
        'storage' => '@project/storage',
        '@ant/rbac/views' => '@vendor/antweb/yii2-user/src/rbac/views',
		'@ant/migrations' => '@vendor/antweb/yii2-core/src/migrations',
		//'@ant/moduleManager' => '@vendor/inspirenmy/yii2-core/src/common/modules/moduleManager',
		//'@common/assets/StyleAsset' => '@vendor/inspirenmy/yii2-core/src/common/assets/StyleAsset',
		//'@frontend/assets/AppAsset' => '@vendor/inspirenmy/yii2-core/src/frontend/assets/AppAsset',
		'@backend/themes/adminlte' => '@vendor/inspirenmy/yii2-core/src/backend/themes/adminlte',
		//'@common/widgets' => '@vendor/inspirenmy/yii2-core/src/common/widgets',
		'@common/config' => '@project/config', // for behaviors use
    ],
	'layout' => 'default',
    'vendorPath' => '@project/vendor',
    'bootstrap' => ['log', 'config', 'queue', 'ant\moduleManager\ModuleAutoLoader'],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to  
            // use your own export download action or custom translation 
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
    ],
    'components' => [
		'encoder' => [
			'class' => 'ant\components\Encoder',
			'hashIdSalt' => env('HASHID_SALT'),
		],
		'projectContainer' => [
			'class' => 'ant\components\ProjectContainer',
		],
		'apiUrlManager' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => Yii::getAlias('@apiUrl'),
			'enablePrettyUrl' => true,
			'enableStrictParsing' => true,
			'showScriptName' => false,
			'rules' => [
			],
		],
        'urlManagerFrontEnd' => [
            'class' => 'yii\web\urlManager',
            //'baseUrl' => @frontendUrl,
            'baseUrl' => Yii::getAlias('@frontendUrl'),
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
		'notifier' => [
           'class' => '\tuyakhov\notifications\Notifier',
		   'on '.\tuyakhov\notifications\Notifier::EVENT_AFTER_SEND => function($event) {
			   if ($event->response === true) {
					if (isset($event->notification->successMessage)) {
						Yii::$app->session->setFlash('success', $event->notification->successMessage);
					}
			   } else if ($event->response !== true) {
					if (YII_DEBUG) throw new \Exception('Response: '.$event->response);
						
					if (isset($event->notification->failMessage)) {
						Yii::$app->session->setFlash('error', $event->notification->failMessage);
					}
			   }
		   },
           'channels' => [
               'mail' => [
                   'class' => '\ant\notifications\channels\MailChannel',
                   'from' => [env('ROBOT_EMAIL') => env('APPLICATION_NAME')],
				   'developerEmail' => env('DEVELOPER_EMAIL'),
               ],
               /*'sms' => [
                   'class' => '\tuyakhov\notifications\channels\TwilioChannel',
                   'accountSid' => '...',
                   'authToken' => '...',
                   'from' => '+1234567890'
               ],
               'database' => [
                    'class' => '\tuyakhov\notifications\channels\ActiveRecordChannel'
               ]*/
           ],
       ],
        'moduleManager' => [
            'class' => 'ant\moduleManager\ModuleManager',
			'moduleAutoloadPaths' => ['@ant', 
				'@common/modules', // Needed for project which have common\modules folder
				env('PACKAGES_PATH', '@vendor/inspirenmy').'/yii2-cms/src/common/modules',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-ecommerce/src', 
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-user/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-core-module/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-importer/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-dashboard/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-affiliate/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-event/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-cart/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-payment/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-member/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-booking/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-subscription/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-library/src',
				env('PACKAGES_PATH', '@vendor/antweb').'/yii2-importer/src',
			],
        ],
        'userConfig' => [
            'class' => 'ant\user\components\UserConfig',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => [\ant\rbac\Role::ROLE_GUEST, \ant\rbac\Role::ROLE_USER],
        ],
		'config' => [
            'class' => 'yii2tech\config\Manager',
			'storage' => [
				'class' => 'yii2tech\config\StorageDb',
				//'idAttribute' => 'key',
				'table' => '{{%config}}',
			],
            'items' => [
                'appName' => [
                    'path' => 'name',
                    'label' => 'Application Name',
                    'rules' => [
                        ['required']
                    ],
                ],
			],
		],
        /*'config' => [
            'class' => 'inspirenmy\config\Configurator',
            'tableName' => '{{%config}}',
            //'tableName' => 'em_config',
            'defaultConfigFile' => '@common/data/default.php',
            // 'handler' => [
            //     'class' => 'testHandler',
            //     'config'=> [
            //         'fileStor age' => 'missing_configs',
            //         'folderStorage' => '@common/data/',
            //     ],
            // ],
        ],*/
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'ant\log\EmailTarget',
                    'levels' => ['error'],
                    'except' => ['yii\web\HttpException:404'],
                    //'categories' => ['yii\db\*'],
                    'message' => [
                       'from' => [env('ROBOT_EMAIL')],
                       'to' => [env('DEVELOPER_EMAIL')],
                       'subject' => (YII_DEBUG ? '[Debug] ' : ''). 'Yii2 Application Log',
                    ],
                    'enabled' => false, // !YII_DEBUG,
                ],
            ],
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'linkAssets' => env('LINK_ASSETS'),
            'appendTimestamp' => YII_ENV_DEV,
        ],
        'user' => [
            'class' => 'ant\user\User',
            'identityClass' => 'ant\user\models\User',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'fileStorage' => [
            'class' => '\trntv\filekit\Storage',
            'baseUrl' => '@storageUrl/source',
            'filesystem' => [
                'class' => 'ant\file\LocalFlysystemBuilder',
                'path' => '@storage/web/source'
            ],
            'as log' => [
                'class' => 'ant\behaviors\FileStorageLogBehavior',
                'component' => 'fileStorage'
            ]
        ],
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
       'encrypter' => [
            'class'=>'\nickcv\encrypter\components\Encrypter',
            'globalPassword'=>'eventmyEncrypterPassword',
            'iv'=>'AWWw7gWE2HhfGzTm',
            'useBase64Encoding'=>true,
            'use256BitesEncoding'=>false,
        ],
        'i18n' => [
            'translations' => [
                'app'=>[
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath'=>'@ant/messages',
                ],
                '*'=> [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath'=>'@ant/messages',
                    'fileMap'=>[
                        'common'=>'common.php',
                        'backend'=>'backend.php',
                        'frontend'=>'frontend.php',
                    ],
                    //'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
                ],
                /* Uncomment this code to use DbMessageSource
                 '*'=> [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceMessageTable'=>'{{%i18n_source_message}}',
                    'messageTable'=>'{{%i18n_message}}',
                    'enableCaching' => YII_ENV_DEV,
                    'cachingDuration' => 3600,
                    'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
                ],
                */
            ],
        ],
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex used to sync queries
            'attempts' => 3,
            //'as log' => \yii\queue\LogBehavior::class,
            // Other driver options
        ],
        'mailer' => [
            'class' => 'ant\mail\QueueableMailer',
            'queue' => 'queue', // name of queue component, or a valid array configuration for it.
			//'viewPath' => '@project/mails',
            'syncMailer' => [ // Any valid mailer should work
                'class' => 'ant\mail\Mailer',
                'viewPath' => '@project/mails',
                'messageConfig' => [
                    //'to' => 'chy1988@gmail.com',
                ],
                'on '.\ant\mail\Mailer::EVENT_SEND_EMAIL_FAIL => function($event) {
                    $message = $event->message;
                    $message->mailer = null; // So that the message can be serialized.

                    if ($message->attempt <= 3) return Yii::$app->mailer->queue($message);
                }
            ],
        ],
		'view' => [
			'as layout' => [
				'class' => 'ant\behaviors\LayoutBehavior',
			],
		],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('DB_DSN', 'mysql:host=localhost;port=3306;dbname='.env('PROJECT_ID', defined('PROJECT_ID') ? PROJECT_ID : null)),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'tablePrefix' => env('DB_TABLE_PREFIX', ''),
            'charset' => 'utf8mb4',
            'enableSchemaCache' => YII_ENV_PROD,
			'on afterOpen' => function($event) { 
				$now = new \DateTime();
				$mins = $now->getOffset() / 60;
				$sgn = ($mins < 0 ? -1 : 1);
				$mins = abs($mins);
				$hrs = floor($mins / 60);
				$mins -= $hrs * 60;
				$offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
				$event->sender->createCommand("SET time_zone='$offset';")->execute(); 
			},
        ],
        /*'mailqueue' => [
            'class' => 'ant\mail\QueueableMailer',
            'queue' => 'queue', // name of queue component, or a valid array configuration for it.
            'syncMailer' => [ // Any valid mailer should work
                'class' => 'yii\swiftmailer\Mailer',
                'viewPath' => '@project/mails',
                'messageConfig' => [
                    //'to' => 'chy1988@gmail.com',
                ],
            ],
        ],
        'mailer' => [
            'class' => 'ant\mail\Mailer',
            'viewPath' => '@project/mails',
            'messageConfig' => [
                //'to' => 'chy1988@gmail.com',
            ],
            'on '.\ant\mail\Mailer::EVENT_SEND_EMAIL_FAIL => function($event) {
                $message = $event->message;
                $message->mailer = null; // So that the message can be serialized.
                return Yii::$app->mailqueue->queue($message);
            }
        ],*/
    ],
	'on beforeAction' => function($event) {
		if (isset(\Yii::$app->mailer->syncMailer)) {
			\Yii::$app->mailer->syncMailer->view = \Yii::$app->view;
		}
    },
];

if (YII_LOCALHOST) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module'
    ];

    $config['components']['cache'] = [
        'class' => 'yii\caching\DummyCache'
    ];
	
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

if (env('SMTP_HOST')) {
    /*$config['components']['mailer']['transport'] = [
        'class' => 'Swift_SmtpTransport',
        'host' => env('SMTP_HOST'),
        'port' => env('SMTP_PORT'),
        'username' => env('SMTP_USERNAME'),
        'password' => env('SMTP_PASSWORD'),
        'encryption' => env('SMTP_ENCRYPTION'),
        'streamOptions' => [
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],
    ];*/
    $config['components']['mailer']['syncMailer']['transport'] = [
        'class' => 'Swift_SmtpTransport',
        'host' => env('SMTP_HOST'),
        'port' => env('SMTP_PORT'),
        'username' => env('SMTP_USERNAME'),
        'password' => env('SMTP_PASSWORD'),
        'encryption' => env('SMTP_ENCRYPTION'),
        'streamOptions' => [
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],
    ];
}

return $config;
