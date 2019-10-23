<?php
namespace ant\helpers;

use yii\helpers\Json;

class ActiveRecordHelper
{
	public static function encodePrimaryKey(\yii\db\ActiveRecord $model) {
		/*if (!($model instanceof \yii\db\ActiveRecord)) {
			throw new InvalidParamException(Yii::t('app', 'The model must be of type ActiveRecord'));
		}*/

		$pk = $model->primaryKey();

		// Check if a primary key for model is valid.
		if ($pk === null || !is_array($pk) || count($pk) == 0) {
			$msg = Yii::t('app', 'Invalid primary key definition: please provide a pk-definition for table {table}', ['table' => $model->tableName()]);
			throw new InvalidConfigException($msg);
		}

		$arrPk = [];
		foreach ($pk as $pkCol) {
			$arrPk[$pkCol] = $model->{$pkCol};
		}
		
		return Json::encode($arrPk);
	}
	
	public static function getAllRelations($model, $nameOnly = false) {
		$stack = [];
		$reflector = new \ReflectionClass($model);
		$baseClassMethods = get_class_methods('yii\db\ActiveRecord');
		foreach ($reflector->getMethods() as $method) {
			if (in_array($method->name, $baseClassMethods)) {
				continue;
			}
			
			$methodReflection = new \ReflectionMethod($model, $method->name);
			
			if ($methodReflection->isPublic() && strpos($method->name, 'get') === 0) {
				try {
					$relation = call_user_func_array([$model, $method->name], []);     
					
					if ($relation instanceof \yii\db\ActiveQuery) {
						$name = lcfirst(preg_replace('/^get/', '', $method->name));
						
						if ($nameOnly) {
							$stack[] = $name;
						} else {
							$stack[$name]['name'] = $name;
							$stack[$name]['method'] = $method->name;
							$stack[$name]['ismultiple'] = $relation->multiple;
							$stack[$name]['modelClass'] = $relation->modelClass;
							$stack[$name]['link'] = $relation->link;
							$stack[$name]['via'] = $relation->via;
						}
					}
				} catch (\ArgumentCountError $ex) {
				} catch (\Exception $ex) {
				}
			}
		}
		return $stack;
	}
	
	public static function duplicate($oldModel, $attributes = null, $relations = null) {
		$className = $oldModel->className();
		//$model = new $className;
        $model = clone $oldModel;
		
		//throw new \Exception('old: '.print_r($oldModel->attributes,1));
		if (!isset($attributes)) $attributes = array_keys($oldModel->attributes);
		if (!isset($relations)) $relations = self::getAllRelations($oldModel, true);
		
		//throw new \Exception(print_r($attributes,1));
		//throw new \Exception(print_r($relations,1));
		foreach ($attributes as $attribute) {
			if (isset($oldModel->{$attribute})) {
				$model->{$attribute} = $oldModel->{$attribute};
			}
		}
		
		$model->isNewRecord = true;
        foreach ($model->primaryKey() as $key) {
			$model->$key = null;
        }
		
		if (!$model->save()) throw new \Exception(\yii\helpers\Html::errorSummary($model));
		
		// Delete all relation models auto created when $model->save()
		/*foreach ((array) $relations as $relation) {
			$related = $model->getRelation($relation['name'])->all();
			
			foreach ($related as $relationModel) {
				$relationModel->delete();
			}
		}*/
		
		// Duplicate relation models
		foreach ((array) $relations as $key => $relation) {
			if (is_int($key)) {
				$nestedRelation = null;
				$relation = $relation;
			} else {
				$nestedRelation = $relation;
				$relation = $key;
			}
			
			if (is_array($relation)) throw new \Exception('Invalid relation name: '.print_r($relation, 1));
			
			$related = $oldModel->getRelation($relation)->all();
			
			if (is_array($related)) {
				foreach ($related as $relationModel) {
					$newRelationModel = self::duplicate($relationModel, null, $nestedRelation);
					$model->link($relation, $newRelationModel);
				}
			} else {
				$newRelationModel = self::duplicate($related, null, $nestedRelation);
				$model->link($relation, $newRelationModel);
			}
		}
		$model->trigger(\ant\behaviors\DuplicableBehavior::EVENT_AFTER_DUPLICATE);
		
		return $model;
	}
	
    public static function parse($className, $params = [], $newAsdefault = true)
    {
        $activeRecord = null;

        //$shortClassName = (new \ReflectionClass(new $className))->getShortName();

        if ($params instanceof $className) {

            return $params;

        } else if (is_numeric($params)) {

            $activeRecord = $className::findOne((int)$params);

        } else if (is_array($params)) {

            $activeRecord = new $className;

            $activeRecord = $activeRecord->load($params) ? $activeRecord : null;
        }

        if($newAsdefault && $activeRecord == null)
        {
            $activeRecord = new $className;
        }

        return $activeRecord;
    }
}
?>
