<?php

namespace ant\widgets\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class NestedSortableColumnAsset extends AssetBundle
{
	public $sourcePath = '@common/widgets/assets/nestedSortable';
	
    public $css = [
		'grid.css',
    ];

    public $js = [
		'Ant.js',
		'Base.js',
		'velocity.js',
		'jquery.helper.min.js',
		'jquery.structure.js',
    ];
	
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
		'yii\jui\JuiAsset',
        '\rmrevin\yii\fontawesome\AssetBundle',
    ];

    public $fonts = [
    ];
}
