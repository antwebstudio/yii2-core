<?php
namespace ant\behaviors;

use yii\db\ActiveRecord;
use Hashids\Hashids;
use ant\helpers\StringHelper;

class FormattedAutoIncreaseColumnBehavior extends \yii\base\Behavior {
	protected $_pattern = '\{([^\:\}]+)\:?([^\}]+)\}';
	protected $_dateTimeFormat = 'Y-m-d H:i:s';

	public $returnIdWhenParseFail = false;
	public $format;
	public $saveToAttribute;
	public $createdDateAttribute;

	//Hashids
	public $hashidsAlphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	public $hashidsSalt 	= '';

	public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
        ];
    }

	protected function renderDate($params) {
		$time = $this->getCreatedTime();
		return date($params[0], $time);
	}

	protected function renderId($params) {
		return str_pad($this->id, $params[0], 0, STR_PAD_LEFT);
	}

	protected function renderYearId($params) {
		$year = date('Y', $this->getCreatedTime());
		return str_pad($this->getLastIdOfYear($year), $params[0], 0, STR_PAD_LEFT);
	}

	protected function renderAlphaId($params)
	{
		return StringHelper::alphaID($this->id, false, $params[0]);
	}

	protected function renderHashids($params)
	{
		$hashids = new Hashids($this->hashidsSalt, $params[0], $this->hashidsAlphabet);

		return $hashids->encode($this->id);
	}

	protected function getCreatedTime() {
		if (isset($this->createdDateAttribute) && isset($this->owner->{$this->createdDateAttribute})) {
			if ($this->owner->{$this->createdDateAttribute} == new \yii\db\Expression('NOW()')) {
				$time = time();
			} else {
				$time = strtotime($this->owner->{$this->createdDateAttribute});
			}
		} else if ($this->owner->isNewRecord) {
			// @TODO make sure this is working, eg. make sure that for newly created model, when this function is called, the isNewRecord property is still equal to true.
			$time = time();
		} else {
			throw new \Exception('Not able to generate formatted id. ');
		}
		return $time;
	}

	protected function getLastIdOfYear($year) {
		$query = $this->owner->find();
		if (!$query->hasMethod('andWhereYear')) throw new \Exception('Query for '.$this->owner->className().' must use ActiveQuery which have ant\behaviors\DateTimeAttributeQueryBehavior for the format pattern "'.$this->format.'" to work. ');
		
		return $query->andWhereYear(['between', $this->createdDateAttribute, $year, $year])->count(); // The new record is already inserted, so count now included the new record. Hence, no need to plus 1.
	}
	
	public function getFormattedId() {
		if (isset($this->formatted_id)) {
			return $this->formatted_id;
		}
		return $this->generateFormattedId();
	}

	public function generateFormattedId() {
		if (isset($this->format)) {
			try {
				return preg_replace_callback('/'.$this->_pattern.'/i', array($this, 'parse'), $this->format);
			} catch (\Exception $ex) {
				if ($this->returnIdWhenParseFail) {
					return $this->id;
				} else {
					throw $ex;
				}
			}
		} else {
			return $this->id;
		}
	}

	protected function getId() {
		return $this->owner->primaryKey;
	}

	protected function parse($input) {
		$name = str_replace(' ', '', ucwords(str_replace('-', ' ', $input[1])));
		return call_user_func(array($this, 'render'.$name), array_slice($input, 2));
	}

	public function afterSave($event) {
		//$this->owner->setIsNewRecord(false);
		//$this->owner->setScenario('update');

		if (strlen($this->owner->{$this->saveToAttribute}) == 0) {
			if (isset($this->saveToAttribute) && $this->saveToAttribute){
				$this->owner->{$this->saveToAttribute} = $this->generateFormattedId();

				if (!$this->owner->save()) throw new \Exception('Failed to save formmated ID for invoice. '.(YII_DEBUG ? \yii\helpers\Html::errorSummary($this->owner) : ''));
			}
		}
	}
}
