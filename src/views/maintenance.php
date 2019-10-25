<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
$this->title = $title;

$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
?>
<div class="maintenance container">
	<h1><?= Html::encode($title) ?></h1>
    <?= $message ?>
</div>
