<?php

return [
	'bootstrap' => [
		'timezone',
	],
    'components' => [
		// Cannot put in common as this will cause console error - console don't have session
		'timezone' => [
            'class' => 'yii2mod\timezone\Timezone',
            'actionRoute' => '/site/timezone' //optional param - full path to page must be specified
        ],
		'assetManager' => [
			//'bundles' => require(__DIR__ . '/assets.php'),
			'converter'=> [
				'class' => 'nizsheanez\assetConverter\Converter',
				//'force' => true,
				//'destinationDir' => 'compiled', //at which folder of @webroot put compiled files
				'parsers' => [
                    'scss' => [ // file extension to parse
                        'class' => 'nizsheanez\assetConverter\Scss',
                        //'output' => 'css', // parsed output file type
                        'options' => [ // optional options
                            //'enableCompass' => false, // default is true
                            'importPaths' => ['@project/themes/'.env('THEME', 'default').'/public/sass', '@project/themes/'.env('THEME', 'default').'/public/sass/extends'], // import paths, you may use path alias here, 
                                // e.g., `['@path/to/dir', '@path/to/dir1', ...]`
                            //'lineComments' => false, // if true — compiler will place line numbers in your compiled output
                            'outputStyle' => 'compressed', // May be `compressed`, `crunched`, `expanded` or `nested`,
                                // see more at http://sass-lang.com/documentation/file.SASS_REFERENCE.html#output_style
                        ],
                    ],
                ],
			]
		],
	],
	'on beforeRequest' => function($event) {
		if (isset(\Yii::$app->session['language'])) {
			\Yii::$app->language = \Yii::$app->session['language'];
		}
		$user = Yii::$app->user->identity;
        if ($user && isset($user->timezone) && $user->timezone) {
            Yii::$app->setTimeZone($user->timezone);
        }
		//Yii::$app->setTimeZone('Asia/Kuala_Lumpur');
	},
];