<?php
$model = new \common\models\ModelClass;
?>

<?= \yii\bootstrap\ButtonDropdown::widget([
    'label' => 'Action',
    'dropdown' => [
        'items' => [
            ['label' => 'DropdownA', 'url' => '/'],
            ['label' => 'DropdownB', 'url' => '#'],
        ],
    ],
]) ?>

<?= \yii\bootstrap\ButtonDropdown::widget([
    'label' => 'Action',
	'split' => true,
    'dropdown' => [
        'items' => [
            ['label' => 'DropdownA', 'url' => '/'],
            ['label' => 'DropdownB', 'url' => '#'],
        ],
    ],
]) ?>

<div>

<?php $modal = \yii\bootstrap\Modal::begin([
    'toggleButton' => ['label' => 'Open Modal', 'class' => 'btn btn-default'], 
	'class' => 'fade in',
	'header' => '<h4>Title</h4>',
	'footer' => '<a data-dismiss="modal" class="btn btn-default">Close</a>',
]) ?>
	Content here
<?php \yii\bootstrap\Modal::end() ?>

</div>

<?= \yii\bootstrap\Progress::widget([
	'percent' => 20,
	'label' => 20,
]) ?>

<?php foreach (['alert-success', 'alert-danger'] as $class): ?>
	<?php \yii\bootstrap\Alert::begin([
		'options' => [
			'class' => $class,
		],
	]) ?>
		Test
	<?php \yii\bootstrap\Alert::end() ?>
<?php endforeach ?>

<?php foreach (['btn-primary', 'btn-default'] as $class): ?>
	<?= \yii\bootstrap\Button::widget([
		'label' => 'Action',
		'options' => ['class' => $class],
	]) ?>
<?php endforeach ?>

<?= \kartik\grid\GridView::widget([
	'autoXlFormat' => true,
	'panel'=>[
		'type' => 'primary',
		'heading' => \yii\bootstrap\ButtonDropdown::widget([
			'label' => 'Header',
			'dropdown' => [
				'items' => [
					['url' => ['/'], 'label' => 'Test'],
					['url' => ['/'], 'label' => 'Test'],
				],
			],
		])
	],
	'dataProvider' => new \yii\data\ActiveDataProvider(['query' => \common\models\ModelClass::find()]),
	'columns' => [
		[
			'header' => 'Action',
			'class' => 'yii\grid\ActionColumn',
			'template' => '{approve} {checkin}',
			'buttons' => [
				'approve' => function($url, $model) {
					return \yii\helpers\Html::a('Approve', ['/event/manage/approve', 'id' => $model->id], ['data-method' => 'post', 'class' => 'btn btn-default btn-xs']);
				},
			],
		],
		'id' => [
			'attribute' => 'id',
			'header' => 'Ticket #',
			'value' => function($data) { return $data->id; },
		],
	]
]) ?>

<?= \yii\grid\GridView::widget([
	'dataProvider' => new \yii\data\ActiveDataProvider(['query' => \common\models\ModelClass::find()]),
	'columns' => [
		[
			'header' => 'Action',
			'class' => 'yii\grid\ActionColumn',
			'template' => '{approve} {checkin}',
			'buttons' => [
				'approve' => function($url, $model) {
					return \yii\helpers\Html::a('Approve', ['/event/manage/approve', 'id' => $model->id], ['data-method' => 'post', 'class' => 'btn btn-default btn-xs']);
				},
			],
		],
		'id' => [
			'attribute' => 'id',
			'header' => 'Ticket #',
			'value' => function($data) { return $data->id; },
		],
	]
]) ?>

<?php $form = \kartik\form\ActiveForm::begin() ?>
	<?= $form->field($model, 'id') ?>
	
	<div class="form-group required">
		<label class="required has-star">Name</label>
		<?= \yii\helpers\Html::textInput('name', '', ['class' => 'form-control']) ?>
	</div>
	
	
	<?= $form->field($model, 'id')->widget(\kartik\date\DatePicker::className(), [
		'options' => ['placeholder' => 'Placeholder'],
		'type' => \kartik\date\DatePicker::TYPE_COMPONENT_APPEND,
		'pluginOptions' =>
		[
			'autoclose' => true,
			'todayHighlight' => true,
			'format' => 'yyyy-mm-dd'
		]
	]) ?>

	<?= $form->field($model, 'class_name')->widget(\kartik\time\TimePicker::className(), [
		'options' => ['placeholder' => 'Placeholder'],
		'pluginOptions' =>
		[
			'showSeconds' => false,
			'showMeridian' => false,
			'minuteStep' => 15,
			'secondStep' => 5,
			'defaultTime' => false,
		]
	]) ?>
<?php \kartik\form\ActiveForm::end() ?>

<ul class="breadcrumb">
	<li><a href="#">Home</a></li>
	<li class="active">Library</li>
</ul>

<?= \yii\bootstrap\Tabs::widget([
    'items' => [
        [
            'label' => 'One',
            'content' => 'Anim pariatur cliche...',
            'active' => true
        ],
        [
            'label' => 'Two',
            'content' => 'Anim pariatur cliche...',
            'options' => ['id' => 'myveryownID'],
        ],
        [
            'label' => 'Example',
        ],
        [
            'label' => 'Dropdown',
            'items' => [
                 [
                     'label' => 'DropdownA',
                     'content' => 'DropdownA, Anim pariatur cliche...',
                 ],
                 [
                     'label' => 'DropdownB',
                     'content' => 'DropdownB, Anim pariatur cliche...',
                 ],
                 [
                     'label' => 'External Link',
                     'url' => 'http://www.example.com',
                 ],
            ],
        ],
    ],
]) ?>

<?= \kartik\switchinput\SwitchInput::widget(['name' => 'test']) ?>