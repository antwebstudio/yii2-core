<?php

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@app/views/layouts/_clear.php')
?>
<link rel="stylesheet" href="https://unpkg.com/onsenui/css/onsenui.css">
<link rel="stylesheet" href="https://unpkg.com/onsenui/css/onsen-css-components.min.css">
<script src="https://unpkg.com/onsenui/js/onsenui.min.js"></script>
<style>
	body { overflow: auto !important; }
	.tabbar { position: fixed !important; }
</style>

<div class="wrap">
	<div class="toolbar toolbar--material">
		<?php /*
		<div class="toolbar__left toolbar--material__left">
			<span class="toolbar-button toolbar-button--material">
				<i class="zmdi zmdi-menu"></i>
			</span>
		</div>
		*/ ?>
		<div class="toolbar__center toolbar--material__center">
			<?= $this->title ?>
		</div>
		<div class="toolbar__right toolbar--material__right">
			<?php /*
			<span class="toolbar-button toolbar-button--material">
				<i class="zmdi zmdi-search"></i>
			</span>
			<span class="toolbar-button toolbar-button--material">
				<i class="zmdi zmdi-favorite"></i>
			</span>
			*/ ?>
			<span class="toolbar-button toolbar-button--material">
				<i class="zmdi zmdi-more-vert"></i>
			</span>
		</div>
	</div>
	
	<?php /*
	
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]); 
	?>

    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => isset(\Yii::$app->menu) ? \Yii::$app->menu->getMainMenu() : [],
    ]); ?>
    <?php NavBar::end(); ?>
	
	*/ ?>

    <?= $content ?>

</div>

<footer class="">
	<div class="tabbar">
		<label class="tabbar__item">
			<input type="radio" name="tabbar-a" checked="checked">
			<button class="tabbar__button">
				<i class="tabbar__icon ion-stop"></i>
				<div class="tabbar__label">One</div>
			</button>
		</label>

		<label class="tabbar__item">
			<input type="radio" name="tabbar-a">
			<button class="tabbar__button">
				<i class="tabbar__icon ion-record"></i>
				<div class="tabbar__label">Two</div>
			</button>
		</label>

		<label class="tabbar__item">
			<input type="radio" name="tabbar-a">
			<button class="tabbar__button">
				<i class="tabbar__icon ion-star"></i>
				<div class="tabbar__label">Three</div>
			</button>
		</label>
	</div>
</footer>
<?php $this->endContent() ?>
