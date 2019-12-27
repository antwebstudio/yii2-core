<?php

use ant\db\Migration;

/**
 * Class m191225_025230_create_model_type
 */
class m191225_025230_create_model_type extends Migration
{
    protected $tableName = '{{%model_type}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'type_name' => $this->string(),
			'title' => $this->string()->null()->defaultValue(null),
			'class_id' => $this->integer()->unsigned()->notNull(),
        ], $this->getTableOptions());

    }

    /**
     * {@inheritdoc}
     */
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
        echo "m191225_025230_create_model_type cannot be reverted.\n";

        return false;
    }
    */
}
