<?php

use ant\components\Migration;

class m181007_054128_create_model_class extends Migration
{
    protected $tableName = '{{%model_class}}';
    
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'class_name' => $this->string(),
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
        echo "m181007_054128_create_model_class cannot be reverted.\n";

        return false;
    }
    */
}
