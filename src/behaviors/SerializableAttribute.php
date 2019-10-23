<?php
namespace ant\behaviors;

class SerializableAttribute extends SerializeBehavior {
	
	public $serializeMethod = self::METHOD_JSON;
}