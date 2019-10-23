<?php

use yii\db\Migration;
use yii\db\Expression;

class m170920_014004_config_data extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->batchInsert('{{%config}}', ['id', 'application', 'key', 'value', 'type', 'label' , 'created_at', 'updated_at'], $this->configDataRow());
    }

    private function configDataRow(){
        return [
            [1, 'app-frontend', 'appconfig.modules.user.profileSetting.firstname', 1, 1,'Firstname' , new Expression('NOW()'), new Expression('NOW()')],
            [2, 'app-frontend', 'appconfig.modules.user.profileSetting.lastname', 1, 1,'Lastname', new Expression('NOW()'), new Expression('NOW()')],
            [3, 'app-frontend', 'appconfig.modules.user.profileSetting.contact', 1, 1,'Contact' , new Expression('NOW()'), new Expression('NOW()')],
            [4, 'app-frontend', 'appconfig.modules.user.profileSetting.discount_rate', 1, 1,'Discount rate' , new Expression('NOW()'), new Expression('NOW()')],
            [5, 'app-frontend', 'appconfig.modules.user.profileSetting.company', 1, 1,'Company Name' , new Expression('NOW()'), new Expression('NOW()')],
            [6, 'app-frontend', 'appconfig.components.maintenanceMode.enabled', 1, 1,'Maintenance mode' , new Expression('NOW()'), new Expression('NOW()')],
            ];
    }

    public function down (){
        Yii::$app->db->createCommand()->truncateTable('{{%config}}')->execute();
    }
}