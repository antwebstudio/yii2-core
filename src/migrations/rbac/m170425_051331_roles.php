<?php

use yii\db\Schema;
use ant\rbac\Migration;

use ant\rbac\Role;
use ant\rbac\rules\AuthenticatedUserRule;

class m170425_051331_roles extends Migration
{
	public function up()
    {
		// Should not removeAll here as the previous migration file m170325_051331_init will removeAll
		// $this->auth->removeAll();

		$developer = $this->auth->createRole(Role::ROLE_DEVELOPER);
		$this->auth->add($developer);

		$superadmin = $this->auth->createRole(Role::ROLE_SUPERADMIN);
		$this->auth->add($superadmin);
		$this->auth->addChild($developer, $superadmin);

		$admin = $this->auth->createRole(Role::ROLE_ADMIN);
		$this->auth->add($admin);
		$this->auth->addChild($superadmin, $admin);

		$dealer = $this->auth->createRole(Role::ROLE_DEALER);
		$this->auth->add($dealer);
		$this->auth->addChild($superadmin, $dealer);

		$user = $this->auth->createRole(Role::ROLE_USER);
		$user->ruleName = \ant\rbac\rules\AuthenticatedUserRule::className();
		$this->auth->add($user);
		$this->auth->addChild($admin, $user);

		$user = $this->auth->createRole(Role::ROLE_GUEST);
		$this->auth->add($user);
		// No need to add guest as user child as every user will auto be guest as well (default roles)
    }

    public function down()
    {
		if (!YII_DEBUG) return false;
		
		$roles = [
			Role::ROLE_DEVELOPER,
			Role::ROLE_ADMIN,
			Role::ROLE_DEALER,
			Role::ROLE_SUPERADMIN,
			Role::ROLE_USER,
			Role::ROLE_GUEST
		];
		
		foreach ($roles as $role) {
			if ($this->auth->getRole($role) !== null) {
				$this->auth->remove($this->auth->getRole($role));
			}
		}
    }
}
