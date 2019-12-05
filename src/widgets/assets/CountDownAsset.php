<?php

namespace ant\widgets\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CountDownAsset extends AssetBundle
{
	public $sourcePath = '@vendor/antweb/yii2-core/src/widgets/assets/count-down';
	
    public $css = [
    ];

    public $js = [
		'countdown-timer.min.js',
    ];
	
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public $fonts = [
    ];
}
