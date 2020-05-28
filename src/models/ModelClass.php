<?php

namespace ant\models;

use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "em_model_class".
 *
 * @property integer $id
 * @property string $class_name
 */
class ModelClass extends \yii\db\ActiveRecord
{
	const CACHE_TAG_PREFIX = 'model_class_';
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%model_class}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'class_name' => 'Class Name',
        ];
    }
	
	public static function getModel($modelClassId, $modelId) {
		$className = self::getClassName($modelClassId);
		return $className::find()->alias('morphModel')->cache(7200)->andWhere(['morphModel.id' => $modelId])->one();
	}

    public static function getClassName($modelClassId) {
		if (!isset($modelClassId)) throw new \Exception('Class ID cannot be null. ');
        return self::find()->cache(7200)->andWhere(['id' => $modelClassId])->one()->class_name;
    }

    public static function getClassId($modelClassName) {
		$modelClassName = is_object($modelClassName) ? get_class($modelClassName) : $modelClassName;
		
		$tagName = static::CACHE_TAG_PREFIX.'_'.$modelClassName;
		
		$dependency = new TagDependency(['tags' => $tagName]);
		
        $model = self::find()->cache(7200, $dependency)->andWhere(['class_name' => $modelClassName])->one();
        if (!isset($model)) {
            $model = new self;
            $model->class_name = $modelClassName;
            if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
			
			if (isset(Yii::$app->cache)) TagDependency::invalidate(Yii::$app->cache, $tagName);
        }
        return $model->id;
    }
}
