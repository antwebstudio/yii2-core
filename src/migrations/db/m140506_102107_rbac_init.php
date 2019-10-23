<?php
use yii\db\Migration;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

class m140506_102107_rbac_init extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

	public function up()
    {
        $authManager = $this->getAuthManager();
		$prefix = $this->db->tablePrefix;
		$itemChildTable = Yii::$app->db->schema->getRawTableName($authManager->itemChildTable);
		$assignmentTable = Yii::$app->db->schema->getRawTableName($authManager->assignmentTable);
		
		$this->dropForeignKey($itemChildTable.'_ibfk_1', $authManager->itemChildTable);
		$this->dropForeignKey($itemChildTable.'_ibfk_2', $authManager->itemChildTable);

		$this->dropForeignKey($assignmentTable.'_ibfk_1', $authManager->assignmentTable);

		$this->alterColumn($authManager->itemTable, 'name', $this->string(100));
		$this->alterColumn($authManager->itemChildTable, 'parent', $this->string(100));
		$this->alterColumn($authManager->itemChildTable, 'child', $this->string(100));

		$this->addForeignKey($itemChildTable.'_ibfk_1', $authManager->itemChildTable, 'parent', $authManager->itemTable, 'name', 'cascade', 'cascade');
		$this->addForeignKey($itemChildTable.'_ibfk_2', $authManager->itemChildTable, 'child', $authManager->itemTable, 'name', 'cascade', 'cascade');

		$this->addForeignKey($assignmentTable.'_ibfk_1', $authManager->assignmentTable, 'item_name', $authManager->itemTable, 'name', 'cascade', 'cascade');
    }

    public function down()
    {
		// $this->dropForeignKey('e_auth_item_child_ibfk_1', '{{%auth_item_child}}');
		// $this->dropForeignKey('e_auth_item_child_ibfk_2', '{{%auth_item_child}}');
		//
		// $this->alterColumn('{{%auth_item}}', 'name', $this->string(64));
		// $this->alterColumn('{{%auth_item_child}}', 'parent', $this->string(64));
		// $this->alterColumn('{{%auth_item_child}}', 'child', $this->string(64));
		//
		// $this->addForeignKey('e_auth_item_child_ibfk_1', '{{%auth_item_child}}', 'parent', '{{%auth_item}}', 'name', 'set null', 'cascade');
		// $this->addForeignKey('e_auth_item_child_ibfk_2', '{{%auth_item_child}}', 'child', '{{%auth_item}}', 'name', 'set null', 'cascade');
    }
}
