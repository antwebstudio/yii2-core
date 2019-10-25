<?php foreach (\Yii::$app->moduleManager->getRegisteredModules() as $module): ?>
	<pre><?= print_r($module, 1) ?></pre>
<?php endforeach ?>