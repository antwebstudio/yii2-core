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
		
		$namespacePath = [];
		foreach ($this->migrationNamespaces as $namespace) {
			$namespacePath[] = $this->invokeMethod($this, 'findMigrationPath', [$namespace]);
		}
		
		Console::Output('Namespaces');
		print_r($this->migrationNamespaces);
		
		Console::Output('Migration Path');
		print_r($this->migrationPath);
		
		Console::Output('Namespace Path');
		print_r($namespacePath);
	}
	
	protected function invokeMethod($object, $methodName, $arguments = []) {
		$class = new \ReflectionClass($object);
		$method = $class->getMethod($methodName);
		$method->setAccessible(true);
		return $method->invokeArgs($object, $arguments);
		
	}
	
	protected function getManager() {
		return Yii::$app->moduleManager;
	}
}