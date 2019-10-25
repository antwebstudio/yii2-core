<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->beginContent('@app/views/layouts/main.php')
?>
<div class="layout-success container">
    <div class="row">
        <div class="col-lg-12 text-center">
			<span class="fas fa-stack fa-lg fa-5x">
				<i class="fas fa fa-circle fa-stack-2x"></i>
				<i class="fas fa fa-check fa-stack-1x fa-inverse"></i>
			</span>

			<?php if (isset($this->blocks['content-header'])): ?>
				<?= $this->blocks['content-header'] ?>
			<?php else: ?>
				<h2 class="heading">
					Congratulations!
				</h2>
			<?php endif ?>

			<?= $content ?>
        </div>
    </div>
</div>
<?php $this->endContent() ?>
