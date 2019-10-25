
<?php $this->beginContent('@app/views/layouts/main.php');?>
	<div class="layout-one-column container">
		<div class="row">
			<div class="col-lg-12">
				<?= \ant\widgets\Alert::widget() ?>
				
				<?= $content ?>
			</div>
		</div>
	</div>
<?php $this->endContent(); ?>
