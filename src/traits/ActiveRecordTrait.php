<?php
namespace ant\traits;

trait ActiveRecordTrait {
	public function withAttributes($attributes = []) {
		$this->setAttributes($attributes);
		return $this;
	}
}