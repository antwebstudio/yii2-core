<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\sidenav\SideNav;
use ant\cms\models\Entry;
use ant\file\models\FileAttachment;

$sideNavItems = isset($this->params['sideNav']['items']) ? $this->params['sideNav']['items'] : null;
?>
<?php $this->beginContent('@app/views/layouts/base.php') ?>
<section>
<div class="layout-right-sidebar container">
	<div class="row">
		<div class="col-md-12 col-lg-8">
			<?php if (isset($this->blocks['content-header'])): ?>
				<?= $this->blocks['content-header'] ?>
			<?php elseif (isset($this->params['title'])): ?>
				<h1 class="page-heading"><?= \yii\helpers\Html::encode($this->params['title']) ?></h1>
			<?php endif ?>
			
			<?= \ant\widgets\Alert::widget() ?>
			
			<?= $content ?>
		</div>
		
		<div class="col-lg-4">
			<?php if (isset($this->blocks['sidebar'])): ?>	
				<?= $this->blocks['sidebar'] ?>	
			<?php elseif (isset($sideNavItems)): ?>
					<div class="list-group list-group-border-0 mb-20">
						<?php foreach ($sideNavItems as $item): ?>
							<?php if (!isset($item['visible']) || $item['visible']): ?>
								<a href="<?= Url::to($item['url']) ?>" class="list-group-item <?= \Yii::$app->menu->isItemActive($item, $this->context->route) ? 'active' : '' ?>">
									<span><i class="icon-home"></i> <?= $item['label'] ?></span>
								</a>
							<?php endif ?>
						<?php endforeach ?>
					</div>
					
			<?php endif ?>
		</div>
	</div>
</div>
</section>
<?php $this->endContent() ?>