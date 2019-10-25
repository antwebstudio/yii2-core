<?php



use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;
use ant\rbac\rules\IsOwnModelRule;
use ant\user\models\UserProfile;
//use ant\controllers\SettingController;

class m170622021848_setting_permissions extends Migration
{
	protected $permissions;

	public function init() {
		$this->permissions = [
			/*SettingController::className() => [
				'index' => ['Show all setting', [Role::ROLE_ADMIN]],
				'build' => ['Build runtime file based on database', [Role::ROLE_ADMIN]],
			]*/
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
