<?php

namespace ant\moduleManager\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M180522081039_permissions extends Migration
{
	protected $permissions;
	
	public function init()
    {
		$this->permissions = [
			\ant\moduleManager\controllers\DefaultController::className() => [
				'index' => ['Module manager', [Role::ROLE_DEVELOPER]],
			],
			\ant\moduleManager\backend\controllers\DefaultController::className() => [
				'index' => ['Module manager', [Role::ROLE_DEVELOPER]],
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
