<?php
use ant\db\Migration;

class m170920_013904_create_config_table extends Migration
{
	protected $tableName = '{{%config}}';
    protected $indexKeys = ['key', 'type'];

    public function init()
    {
        
    }
    
    public function safeUp()
    {
        $this->createTable($this->tableName, [
			'id' => $this->primaryKey()->unsigned(),
			'key' => $this->string(100)->notNull(),
			'application' => $this->string(255)->notNull(),
			'value' => $this->string(255)->notNull(),
			'type' => $this->smallInteger(4)->unsigned()->notNull(),
			'label' => $this->string(255)->defaultValue(NULL),
			'created_at' => $this->datetime()->notnull(),
			'updated_at' => $this->datetime()->defaultValue(NULL),
        ], $this->getTableOptions());
    }

    public function safeDown()
    { 
       $this->dropTable($this->tableName);
    }
}