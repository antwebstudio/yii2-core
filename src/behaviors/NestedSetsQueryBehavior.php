<?php
namespace ant\behaviors;

class NestedSetsQueryBehavior extends \creocoder\nestedsets\NestedSetsQueryBehavior {
	public function exceptRoot() {
		return $this->owner->andWhere(['!=', 'depth', 0]);
	}
}