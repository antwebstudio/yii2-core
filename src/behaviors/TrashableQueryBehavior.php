<?php
namespace ant\behaviors;

class TrashableQueryBehavior extends \yii2tech\ar\softdelete\SoftDeleteQueryBehavior {
	public function events() {
		return [
			\yii\db\ActiveQuery::EVENT_INIT => function($event) {
				// Apply default scope
				$query = $event->sender;
				
				$modelClass = $query->modelClass;
				if ($modelClass::hasGlobalScope('notDeleted')) {
					$query->notDeleted();
				}
			},
		];
	}
}