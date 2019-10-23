<?php

use yii\db\Schema;
use ant\rbac\Migration;

use ant\rbac\Role;
use ant\rbac\Permission;

class m170425_051346_permissions extends Migration
{
	public function up()
    {
		$loginToBackend = $this->auth->createPermission(Permission::of('login')->type('backend')->name);
		$loginToBackend->description = 'Login to backend';
		$this->auth->add($loginToBackend);
		$this->auth->addChild($this->auth->getRole(Role::ROLE_ADMIN), $loginToBackend);
    }

    public function down()
    {
		$this->auth->remove($this->auth->getPermission(Permission::of('login')->type('backend')->name));
    }
}
