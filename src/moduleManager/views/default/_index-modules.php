
<table class="table">
<?php foreach (\Yii::$app->modules as $name => $moduleConfig): ?>
	<?php
		try {
			$module = \Yii::$app->getModule($name);
			$exception = false;
		} catch (\Exception $ex) {
			$exception = true;
		}
	?>
	<tr>
		<td><?= $name ?></td><td><?= get_class($module) ?></td>
		<td>
			<?php if ($exception): ?> 
				<span class="label-danger label">Failed</span> <?= $ex->getMessage() ?>
			<?php endif ?>
		</td>
	</tr>
<?php endforeach ?>
</table>