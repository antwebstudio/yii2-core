<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
	'name' => 'Event.my',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'modules' => [
		'event' => [
			'class' => 'backend\modules\event\Module',
		]
	],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'ant\user\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-eventmy', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            //'name' => 'advanced-backend',
            'name' => 'eventmy-login',
        ],
            
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
	'modules' => [
		/*
			https://github.com/developeruz/yii2-db-rbac
			/permit/access/role - manage roles
			/permit/access/permission - manage access

		*/
		 /*
		'permit' => [
            'class' => 'developeruz\db_rbac\Yii2DbRbac',
        ],
		'gridview' => ['class' => 'kartik\grid\Module'],
		*/
		/*
			https://github.com/johnitvn/yii2-rbac-plus
			/rbac/rule
			/rbac/permission
			/rbac/role
			/rbac/assignment

		*/
		/*
		'rbac-plus' =>  [
			'class' => 'johnitvn\rbacplus\Module',
			'userModelClassName'=>null,
			'userModelIdField'=>'id',
			'userModelLoginField'=>'username',
			'userModelLoginFieldLabel'=>null,
			'userModelExtraDataColumls'=>null,
			'beforeCreateController'=>null,
			'beforeAction'=>null
		], 
		*/ 
		/*
			https://github.com/mdmsoft/yii2-admin
		*/
		/*
		'admin' => [
			
		],
		*/
		/*
			https://github.com/yii2mod/yii2-rbac
			rbac/assignment
			rbac/role
			rbac/rule
			rbac/route
		*/
		/*
		'rbac' => [
			'class' => 'yii2mod\rbac\Module',
			// Some controller property maybe need to change.
			'controllerMap' => [
				'assignment' => [
					'class' => 'yii2mod\rbac\controllers\AssignmentController',
					'userIdentityClass' => 'ant\user\models\User',
					'searchClass' => [
						'class' => 'yii2mod\rbac\models\search\AssignmentSearch',
						'pageSize' => 10,
					],
					'idField' => 'id',
					'usernameField' => 'username',
					'gridViewColumns' => [
						 'id',
						 'username',
						 'email'
					 ]
				],
				'role' => [
					'class' => 'yii2mod\rbac\controllers\RoleController',
					'searchClass' => [
						'class' => 'yii2mod\rbac\models\search\AuthItemSearch',
						'pageSize' => 10,
					],
				],
				'rule' => [
					'class' => 'yii2mod\rbac\controllers\RuleController',
					'searchClass' => [
						'class' => 'yii2mod\rbac\models\search\BizRuleSearch',
						'pageSize' => 10
					],
				],
				'route' => [
					'class' => 'yii2mod\rbac\controllers\RouteController',
					// for example: exclude `api, debug and gii` modules from list of routes
					'modelClass' => [
						'class' => 'yii2mod\rbac\models\RouteModel',
						'excludeModules' => ['api', 'debug', 'gii'],
					],
				],
			]
		],
		*/
	],
    'params' => $params,
];
