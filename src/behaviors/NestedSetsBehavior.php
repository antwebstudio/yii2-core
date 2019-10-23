<?php
namespace ant\behaviors;

class NestedSetsBehavior extends \creocoder\nestedsets\NestedSetsBehavior {
	public function hasOperation() {
		return isset($this->operation);
	}
	
	public function attachedToTree() {
		$root = $this->treeAttribute !== false ? $this->owner->getAttribute($this->treeAttribute) : null;
		$left = $this->owner->getAttribute($this->leftAttribute);
		$right = $this->owner->getAttribute($this->rightAttribute);
		return isset($left) && isset($right) && (isset($root) || $this->treeAttribute === false);
	}
}