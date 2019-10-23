<?php

use ant\components\Migration;

class m181106_091630_create_model_private_url extends Migration
{
    protected $tableName = '{{%model_private_url}}';
	
    public function safeUp()
    {
		
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
			'model_class_id' => $this->integer()->unsigned()->notNull(),
			'model_id' => $this->integer()->unsigned()->notNull(),
            'slug' => $this->string()->notNull(),
			'is_default' => $this->smallInteger(1)->notNull(),
			'is_unique' => $this->smallInteger(1)->notNull(),
        ], $this->getTableOptions());
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
        echo "m181106_091630_create_model_private_url cannot be reverted.\n";

        return false;
    }
    */
}
