<?php
namespace ant\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class DuplicableBehavior extends Behavior {
	const EVENT_AFTER_DUPLICATE = 'duplicate';
	
	public $safeAttributesOnly = false;
	public $attributes = null;
	public $relations = null;
	
	public function _duplicate($relations = []) {
        return \ant\helpers\ActiveRecordHelper::duplicate($this->owner, $this->attributes, $this->relations);
    }

	public function duplicate(array $newAttributeValues = [], $save = true)
	{
		return $this->_duplicate();
	}
}