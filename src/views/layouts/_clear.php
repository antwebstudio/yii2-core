<?php
use yii\helpers\Html;
/* @var $this \yii\web\View */
/* @var $content string */

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
	<head>
		<?php if (YII_DEBUG): ?>
			<meta name="robots" content="noindex,nofollow">
		<?php endif ?>
	    <meta charset="<?php echo Yii::$app->charset ?>"/>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <title><?= Html::encode($this->title) ?></title>
	    <?php $this->head() ?>
	    <?= Html::csrfMetaTags() ?>
	</head>
	<body class="system module-<?= $this->context->module->id ?> controller-<?= $this->context->id ?> action-<?= $this->context->action->id ?>">
		<?php $this->beginBody() ?>
		    <?= $content ?>
		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
