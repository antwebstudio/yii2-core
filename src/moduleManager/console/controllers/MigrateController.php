<?php
namespace ant\moduleManager\console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\ArrayHelper;

class MigrateController extends \yii\console\controllers\MigrateController {
	public $migrationNamespaces;
	public $migrationType = 'db';
	
	public function init() {
		$this->migrationNamespaces = ArrayHelper::merge($this->manager->getMigrationNamespaces($this->migrationType), (array) $this->migrationNamespaces);
		$this->migrationPath = ArrayHelper::merge($this->manager->getMigrationPath($this->migrationType), (array) $this->migrationPath);
		
		return parent::init();
	}
	
	public function actionTest($moduleId = null) {
		if (isset($moduleId)) Console::Output($moduleId);
		print_r($this->migrationNamespaces);
		print_r($this->migrationPath);
	}
	
	protected function getManager() {
		return Yii::$app->moduleManager;
	}
}