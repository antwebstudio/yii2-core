<?php

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class m200506_063058_maintenance_permissions extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\ant\backend\controllers\MaintenanceController::className() => [
				'clear-cache' => ['Clear cache', [Role::ROLE_ADMIN]],
			],
		];
		
		parent::init();
	}
	
	public function up()
    {
		$this->addAllPermissions($this->permissions);
    }

    public function down()
    {
		$this->removeAllPermissions($this->permissions);
    }
}
