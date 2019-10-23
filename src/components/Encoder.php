<?php
namespace ant\components;

class Encoder extends \yii\base\Component {
    const HASHIDS_ALPHABET  = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const HASHIDS_ALPHANUMERIC_LOWERCASE = '0123456789abcdefghijklmnopqrstuvwxyz';
	
	public $hashIdSalt;
	
	public function generateHashId() {
		$format = '{hashids:' . $this->minimumLength .  '}';
	}
	
	public function encode($id, $minimumLength = 4) {
		$hashid = new \Hashids\Hashids($this->hashIdSalt, $minimumLength, self::HASHIDS_ALPHANUMERIC_LOWERCASE);
		return $hashid->encode($id);
	}
}