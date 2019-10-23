<?php
namespace ant\moduleManager\console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class DefaultController extends Controller {
	public function actionEnabled() {
		Console::output('Enabled Modules: ');
		Console::output('===================');
		foreach ($this->manager->getEnabledModules() as $module) {
			Console::output($module);
		}
	}
	
	public function actionInfo() {
		Console::output('Enabled Modules: ');
		Console::output('===================');
		foreach ($this->manager->getEnabledModules() as $module) {
			Console::output($module);
		}
		
		Console::output('-----------------------------------------');
		
		Console::output('Registered Modules: ');
		Console::output('===================');
		foreach ($this->manager->getRegisteredModules() as $module) {
			Console::output($module);
		}
		
		Console::output('-----------------------------------------');
		
		Console::output('Autoload Path: ');
		Console::output('===================');
		foreach ($this->manager->moduleAutoloadPaths as $path) {
			Console::output($path);
		}
		
		Console::output('-----------------------------------------');
	}
	
	/*
	public function actionRemove($moduleId) {
		if ($this->confirm('Are you sure to remove module "'.$moduleId.'"? ')) {
			if (Yii::$app->moduleManager->removeModule($moduleId)) {
				Console::output('Module "'.$moduleId.'" is successfully removed. ');
			} else {
				Console::output('Failed to remove module. ');
			}
		}
	}
	*/
	public function actionDisable($moduleId) {
		if (Yii::$app->moduleManager->disable($moduleId) !== false) {
			Console::output('Module "'.$moduleId.'" is successfully disabled. ');
		} else {
			Console::output('Failed to disable module. ');
		}
	}
	
	public function actionEnable($moduleId) {
		if (Yii::$app->moduleManager->enable($moduleId) !== false) {
			Console::output('Module "'.$moduleId.'" is successfully enabled. ');
		} else {
			Console::output('Failed to enable module. ');
		}
	}
	
	protected function getManager() {
		return Yii::$app->moduleManager;
	}
}