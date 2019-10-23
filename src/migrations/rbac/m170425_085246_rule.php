<?php

use yii\db\Schema;

use ant\rbac\Migration;
use ant\rbac\rules\IsOwnModelRule;

class m170425_085246_rule extends Migration
{
	public function up()
    {
		$this->auth->add(new IsOwnModelRule);
    }

    public function down()
    {
		$this->auth->remove($this->auth->getRule(IsOwnModelRule::RULE_NAME));
    }
}
