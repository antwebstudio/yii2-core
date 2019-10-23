<?php

use ant\components\Migration;

/**
 * Class m190729_040538_create_model_expiration
 */
class m190729_040538_create_model_expiration extends Migration
{
    protected $tableName = '{{%model_expiration}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'model_class_id' => $this->integer()->unsigned()->notNull(),
            'model_id' => $this->integer()->unsigned()->notNull(),
			'expire_at' => $this->timestamp()->defaultValue(null),
			'renew_count' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0),
			'renewed_at' => $this->timestamp()->defaultValue(null),
			'created_at' => $this->timestamp()->defaultValue(null),
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
        echo "m190729_040538_create_model_expiration cannot be reverted.\n";

        return false;
    }
    */
}
