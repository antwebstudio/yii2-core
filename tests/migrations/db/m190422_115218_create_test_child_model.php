<?php

use ant\db\Migration;

/**
 * Class m190422_115218_create_test_child_model
 */
class m190422_115218_create_test_child_model extends Migration
{
	protected $tableName = '{{%test_child}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'test_id' => $this->integer()->null()->defaultValue(null),
        ], $this->getTableOptions());

        $this->addForeignKeyTo('{{%test}}', 'test_id', 'cascade', 'cascade');
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
        echo "m190422_115218_create_test_child_model cannot be reverted.\n";

        return false;
    }
    */
}
