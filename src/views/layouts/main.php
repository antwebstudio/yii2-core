<?php

use yii\helpers\Html;

?>
<?php $this->beginContent('@app/views/layouts/base.php'); ?>

<div class="layout-main container">
	<div class="row">
		<div class="col-lg-12">
			<?php if (isset($this->blocks['content-header'])): ?>
				<?= $this->blocks['content-header'] ?>
			<?php elseif (isset($this->params['title'])): ?>
				<h1 class="page-heading"><?= \yii\helpers\Html::encode($this->params['title']) ?></h1>
			<?php endif ?>
			
			<?php /* 
			Note: Alert should be put at one-column
			<?= Alert::widget() ?>
			 */ ?>
		</div>
	</div>
</div>

<?= $content ?>

<?php $this->endContent(); ?>