<?php
namespace ant\moduleManager\console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class RbacMigrateController extends \ant\moduleManager\console\controllers\MigrateController {
	public $migrationNamespaces;
	public $migrationType = 'rbac';
	
	protected function createMigration($class)
    {
        $this->includeMigrationFile($class);
        return new $class();
    }
}