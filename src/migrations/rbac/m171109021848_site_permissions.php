<?php



use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;
use ant\rbac\rules\IsOwnModelRule;
use ant\user\models\UserProfile;
use frontend\controllers\SiteController;

class m171109021848_site_permissions extends Migration
{
	protected $permissions;

	public function init() {
		$this->permissions = [
			SiteController::className() => [
				'login' => ['Login', [Role::ROLE_GUEST]],
				'erorr' => ['Error', [Role::ROLE_GUEST]],
				'logout' => ['LogOut', [Role::ROLE_USER]],
				'index' =>  ['index', [Role::ROLE_USER]],
				'test' => ['Build runtime file based on database', [Role::ROLE_GUEST]],
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
