<?php

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@frontend/views/layouts/_clear.php')
?>
<div class="wrap">

    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse',
        ],
    ]); ?>

    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => isset(\Yii::$app->menu) ? \Yii::$app->menu->getMainMenu() : [],
    ]); ?>
    <?php NavBar::end(); ?>

	<?php if (isset($this->blocks['page-header'])): ?>
		<?= $this->blocks['page-header'] ?>
	<?php endif; ?>

    <?= $content ?>

</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->name ?> <?= date('Y') ?></p>
    </div>
</footer>
<?php $this->endContent() ?>
