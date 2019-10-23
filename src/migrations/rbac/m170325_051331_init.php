<?php

use yii\db\Schema;
use ant\rbac\Migration;

use ant\rbac\Role;

class m170325_051331_init extends Migration
{
	public function up()
    {
		$this->auth->removeAll();
    }

    public function down()
    {
		if (!YII_DEBUG) return false;
    }
}
