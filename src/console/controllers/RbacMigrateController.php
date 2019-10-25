<?php

namespace ant\console\controllers;

use yii\console\controllers\MigrateController;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class RbacMigrateController extends MigrateController
{
    /**
     * Creates a new migration instance.
     * @param string $class the migration class name
     * @return \ant\rbac\Migration the migration instance
     */
    protected function createMigration($class)
    {
        $class = trim($class, '\\');
        if (strpos($class, '\\') === false) {
            $file = $this->migrationPath . DIRECTORY_SEPARATOR . $class . '.php';
            require_once($file);
        }

        return new $class();
    }
}
