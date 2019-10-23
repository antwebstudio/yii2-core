<?php
namespace ant\behaviors;

class TrashableBehavior extends \yii2tech\ar\softdelete\SoftDeleteBehavior {
	public $softDeleteAttributeValues = [
        'is_deleted' => true
	];
	
	protected function softDeleteInternal()
    {
        $result = false;
        if ($this->beforeSoftDelete()) {
            //$attributes = $this->owner->getDirtyAttributes();
            foreach ($this->softDeleteAttributeValues as $attribute => $value) {
                if (!is_scalar($value) && is_callable($value)) {
                    $value = call_user_func($value, $this->owner);
                }
                $attributes[$attribute] = $value;
            }
            $result = $this->owner->updateAttributes($attributes);
            $this->afterSoftDelete();
        }
        return $result;
    }
}