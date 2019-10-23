<?php  
namespace ant\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\FormatConverter;
use ant\helpers\DateTime;

class DateTimeAttributeBehavior extends Behavior {
	
	/*
	 * 'attributes' => [
	 *		'startDatetime' => ['start_date', 'start_time'], // use array to set datetime which date and time attribute is separated
	 *		'endDatetime' => ['end_date', 'end_time'],
	 * ],
	*/
	public $attributes = [];
	public $format = 'php:Y-m-d H:i:s'; // display format
	public $timeFormat = 'php:H:i';
	public $dateFormat = 'php:Y-m-d';
	protected $_displayFormat = null;
	
	public function events() {
		return [
			ActiveRecord::EVENT_BEFORE_INSERT 	=> 'beforeSave',
			ActiveRecord::EVENT_BEFORE_UPDATE 	=> 'beforeSave',
			ActiveRecord::EVENT_AFTER_FIND 		=> 'afterFind',
		];
	}

	public function afterFind(){
		foreach ($this->attributes as $key => $attribute) {
			if (is_array($attribute)) {
				list($dateAttribute, $timeAttribute) = $attribute;
				$attribute = $key;
				// Join
				$this->owner->{$attribute} = $this->owner->{$dateAttribute}.' '.$this->owner->{$timeAttribute};

				// Process
				//$this->owner->{$attribute} = \Yii::$app->formatter->asDatetime($this->owner->{$attribute}, $this->displayFormat);

				// Separate
				$this->owner->{$dateAttribute} = \Yii::$app->formatter->asDate($this->owner->{$attribute}, $this->dateFormat);
				$this->owner->{$timeAttribute} = \Yii::$app->formatter->asTime($this->owner->{$attribute}, $this->timeFormat);
			} else if (!($this->owner->{$attribute} instanceof \yii\db\Expression)) {
				// Process
				$this->owner->{$attribute} = \Yii::$app->formatter->asDatetime($this->owner->{$attribute}, $this->displayFormat);
			}
		}
	}
	
	public function beforeSave($event) {
		foreach ($this->attributes as $key => $attribute) {
			if (is_array($attribute)) {
				list($dateAttribute, $timeAttribute) = $attribute;
				$attribute = $key;
				// Join
				$this->owner->{$attribute} = $this->owner->{$dateAttribute}.' '.$this->owner->{$timeAttribute};

				// Process
				$this->owner->{$attribute} = new \DateTime($this->owner->{$attribute}, new \DateTimeZone($this->displayTimezone));
				$dateTime = $this->owner->{$attribute}->setTimezone(new \DateTimeZone($this->saveTimezone));
				$this->owner->{$attribute} = $dateTime->format($this->parseFormat($this->saveFormat));

				// Separate
				$this->owner->{$dateAttribute} = $dateTime->format($this->parseFormat($this->dateFormat));
				$this->owner->{$timeAttribute} = $dateTime->format($this->parseFormat($this->timeFormat));
			} else if (!($this->owner->{$attribute} instanceof \yii\db\Expression)) {
				// Process
				$this->owner->{$attribute} = new \DateTime($this->owner->{$attribute}, new \DateTimeZone($this->displayTimezone));
				$dateTime = $this->owner->{$attribute}->setTimezone(new \DateTimeZone($this->saveTimezone));
				$this->owner->{$attribute} = $dateTime->format($this->parseFormat($this->saveFormat));
			}
		}
	}

	public function getDisplayTimezone() {
		return is_string(\Yii::$app->timezone) ? \Yii::$app->timezone : \Yii::$app->timezone->name;
	}

	public function getSaveTimezone() {
		return \Yii::$app->formatter->defaultTimeZone;
	}

	public function getSaveFormat() {
		return 'php:'.DateTime::FORMAT_MYSQL;
	}

	public function setDisplayFormat($displayFormat) {
		$this->_displayFormat = $displayFormat;
	}

	public function getDisplayFormat() {
		if ($this->_displayFormat === null) {
			$this->_displayFormat = $this->format;
		}
		return $this->_displayFormat;
	}

	protected function parseFormat($format, $type = 'datetime')
    {
        if (strncmp($format, 'php:', 4) === 0) {
            return substr($format, 4);
        } elseif ($format != '') {
            return FormatConverter::convertDateIcuToPhp($format, $type);
        } else {
            throw new InvalidConfigException("Error parsing '{$type}' format.");
        }
    }
}