<?php  
namespace ant\behaviors;

class TimestampQueryBehavior extends \yii\base\Behavior {
	/**
     * @inheritdoc
     *
     * In case, when the [[value]] is `null`, the result of the PHP function [time()](http://php.net/manual/en/function.time.php)
     * will be used as value.
     */
    public function latestFirst()
    {
		return $this->owner->orderBy('created_at DESC');
    }
}