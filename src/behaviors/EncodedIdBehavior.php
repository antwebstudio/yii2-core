<?php
namespace ant\behaviors;

use Yii;

class EncodedIdBehavior extends \ant\behaviors\FormattedAutoIncreaseColumnBehavior {
    const HASHIDS_ALPHABET  = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	public $minimumLength = 6;
	public $saveToAttribute = 'encoded_id';
	public $hashidsAlphabet = self::HASHIDS_ALPHABET;
	
	public function init() {
		if (!isset(Yii::$app->encoder)) throw new \Exception('Please setup ant\components\Encoder to use this behavior. ');
		if (!Yii::$app->encoder->hashIdSalt) throw new \Exception('Hash id salt is not set. ');
		
		$this->format = '{hashids:' . $this->minimumLength .  '}';
		$this->hashidsSalt = Yii::$app->encoder->hashIdSalt;
		
	}
	
	public function getEncodedId() {
		return $this->owner->{$this->saveToAttribute};
	}
}