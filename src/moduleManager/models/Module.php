<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace ant\moduleManager\models;

use Yii;

/**
 * This is the model class for table "module_enabled".
 *
 * @property string $module_id
 */
class Module extends \yii\db\ActiveRecord
{

    const CACHE_ID_ALL_IDS = 'enabledModuleIds';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%module}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id'], 'required'],
            [['module_id'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'module_id' => 'Module ID',
        ];
    }

    public function afterDelete()
    {
		$this->flushCache();
        return parent::afterDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
		$this->flushCache();
        return parent::afterSave($insert, $changedAttributes);
    }
	
	protected function flushCache() {
		if (isset(Yii::$app->cache)) {
			Yii::$app->cache->delete(self::CACHE_ID_ALL_IDS);
		}
	}

    public static function getEnabledIds()
    {
        $enabledModules = isset(Yii::$app->cache) ? Yii::$app->cache->get(self::CACHE_ID_ALL_IDS) : false;
        if ($enabledModules === false) {
            $enabledModules = [];
            foreach (self::find()->all() as $em) {
                $enabledModules[] = $em->module_id;
            }
            if (isset(Yii::$app->cache)) Yii::$app->cache->set(self::CACHE_ID_ALL_IDS, $enabledModules);
        }

        return $enabledModules;
    }

}
