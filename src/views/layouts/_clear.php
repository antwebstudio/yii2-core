<?php
use yii\helpers\Html;
/* @var $this \yii\web\View */
/* @var $content string */

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
	<head>
		<?php if (YII_DEBUG): ?><meta name="robots" content="noindex"><?php endif ?>
	    <meta charset="<?php echo Yii::$app->charset ?>"/>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <title><?php echo Html::encode($this->title) ?></title>
	    <?php $this->head() ?>
	    <?php echo Html::csrfMetaTags() ?>
	</head>
	<body class="system module-<?= $this->context->module->id ?> controller-<?= $this->context->id ?> action-<?= $this->context->action->id ?>">
		<?php $this->beginBody() ?>
		    <?= $content ?>
		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
