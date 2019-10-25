<?php
?>
Note: You may need to clear cache if you can't see the module which is just enabled here.

<?= \yii\bootstrap\Tabs::widget([
	'items' => [
		[
            'label' => 'General',
            'content' => $this->render('_index-general', []),
            'active' => true
        ],
		[
            'label' => 'Modules Registered',
            'content' => $this->render('_index-registered', []),
        ],
		[
            'label' => 'Modules Loaded',
            'content' => $this->render('_index-modules', []),
        ],
	],
]) ?>