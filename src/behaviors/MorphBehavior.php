<?php 
namespace ant\behaviors;

use Yii;
use ant\models\ModelClass;

class MorphBehavior extends \yii\base\Behavior 
{
	public function morphOne($modelClass, $morphName) {
		$modelIdAttribute = $morphName.'_id';
		$classIdAttribute = $morphName.'_class_id';
		return $this->owner->hasOne($modelClass, [$modelIdAttribute => 'id'])
			->onCondition([$classIdAttribute => \ant\models\ModelClass::getClassId($this->owner)]);
	}
	
	public function morphBelongsTo($morphName) {
		$foreignClassIdAttribute = $morphName.'_class_id';
		$foreignModelIdAttribute = $morphName.'_id';
		return $this->owner->hasOne(\ant\models\ModelClass::getClassName($this->owner->{$foreignClassIdAttribute}), ['id' => $foreignModelIdAttribute]);
	}
	
	/*public function morphManyToMany($modelClass, $foreignTableForeignKey, $viaTable) {
		$query = $this->owner->hasMany($modelClass, ['id' => 'tenant_id'])
			->viaTable($viaTable, ['model_id' => 'id'], function ($query) {
				$query->andWhere([
					$viaTable.'.model_class_id' => \ant\models\ModelClass::getClassId(get_class($this->owner)),
				]);
			}
		);
	
		return $query;
	}*/
}