<?php

namespace ant\moduleManager\migrations\db;

use ant\components\Migration;

class M180201075218_create_module extends Migration
{
	protected $tableName = '{{%module}}';
	
    public function safeUp()
    {
		$this->createTable($this->tableName, array(
            'id' => $this->primaryKey()->unsigned(),
            'module_id' => $this->string(100)->notNull(),
		), $this->getTableOptions());
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180201075218_create_module cannot be reverted.\n";

        return false;
    }
    */
}
