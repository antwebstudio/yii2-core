<?php

use ant\db\Migration;

/**
 * Class m190422_114628_alter_test_model
 */
class m190422_114628_alter_test_model extends Migration
{
	protected $tableName = '{{%test}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		
        $this->addColumn($this->tableName, 'dynamic_form_id', $this->integer()->unsigned());
		
		$this->addForeignKeyTo('{{%dynamic_form}}', 'dynamic_form_id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn($this->tableName, 'dynamic_form_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190422_114628_alter_test_model cannot be reverted.\n";

        return false;
    }
    */
}
