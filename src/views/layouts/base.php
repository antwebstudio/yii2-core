<?php
/* @var $this \yii\web\View */
/* @var $content string */
\rmrevin\yii\fontawesome\AssetBundle::register($this);

$this->beginContent('@app/views/layouts/_clear.php')
?>
<div class="wrap">

	<?php if (isset(Yii::$app->params['bsVersion']) && Yii::$app->params['bsVersion'] >= 4): ?>
		<?php \yii\bootstrap4\NavBar::begin([
			'brandLabel' => Yii::$app->name,
			'brandUrl' => Yii::$app->homeUrl,
		]) ?>

			<?= \yii\bootstrap4\Nav::widget([
				'options' => ['class' => 'navbar-nav ml-auto'],
				'items' => isset(\Yii::$app->menu) ? \Yii::$app->menu->getMainMenu() : [],
			]) ?>
		
		<?php \yii\bootstrap4\NavBar::end() ?>
	<?php else: ?>
		<?php \yii\bootstrap\NavBar::begin([
			'brandLabel' => Yii::$app->name,
			'brandUrl' => Yii::$app->homeUrl,
			'options' => [
				'class' => 'navbar-inverse',
			],
		]) ?>

			<?= \yii\bootstrap\Nav::widget([
				'options' => ['class' => 'navbar-nav navbar-right'],
				'items' => isset(\Yii::$app->menu) ? \Yii::$app->menu->getMainMenu() : [],
			]) ?>
		
		<?php \yii\bootstrap\NavBar::end() ?>
		
	<?php endif ?>

	<?php if (isset($this->blocks['page-header'])): ?>
		<?= $this->blocks['page-header'] ?>
	<?php endif ?>

    <?= $content ?>

</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->name ?> <?= date('Y') ?></p>
    </div>
</footer>
<?php $this->endContent() ?>
