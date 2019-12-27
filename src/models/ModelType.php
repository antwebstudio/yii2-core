<?php

namespace ant\models;

use Yii;
use ant\models\ModelClass;

/**
 * This is the model class for table "em_model_class".
 *
 * @property integer $id
 * @property string $class_name
 */
class ModelType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%model_type}}';
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
        ];
    }
	
	public static function getModel($modelClassId, $modelId) {
		$className = self::getClassName($modelClassId);
		return $className::findOne($modelId);
	}

    public static function getTypeName($modelTypeId) {
        return self::findOne($modelTypeId)->type_name;
    }

    public static function getTypeId($modelClassName, $typeName) {
		$modelClassId = ModelClass::getClassId($modelClassName);
		$model = self::find()->andWhere(['class_id' => $modelClassId, 'type_name' => $typeName])->one();
		
        if (!isset($model)) {
            $model = new self;
            $model->class_id = $modelClassId;
			$model->type_name = $typeName;
            if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
        }
        return $model->id;
    }
}
