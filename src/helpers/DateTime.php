<?php
namespace ant\helpers;

use Yii;

class DateTime extends \DateTime {
	/*
	 * const string ATOM = "Y-m-d\TH:i:sP" ;
	 * const string COOKIE = "l, d-M-y H:i:s T" ;
	 * const string ISO8601 = "Y-m-d\TH:i:sO" ;
	 * const string RFC822 = "D, d M y H:i:s O" ;
	 * const string RFC850 = "l, d-M-y H:i:s T" ;
	 * const string RFC1036 = "D, d M y H:i:s O" ;
	 * const string RFC1123 = "D, d M Y H:i:s O" ;
	 * const string RFC2822 = "D, d M Y H:i:s O" ;
	 * const string RFC3339 = "Y-m-d\TH:i:sP" ;
	 * const string RSS = "D, d M Y H:i:s O" ;
	 * const string W3C = "Y-m-d\TH:i:sP" ;
	 * */
	const FORMAT_MYSQL = 'Y-m-d H:i:s';
	const FORMAT_MYSQL_DATE = 'Y-m-d';
	const FORMAT_MYSQL_TIME = 'H:i:s';

	private $_toStringFormat;
	private $_timestamp;
	
	public function __construct($string = 'now', $timezone = null) {
		//if (!isset($timezone)) $timezone = new DateTimeZone('Asia/Kuala_Lumpur');
		
		if (is_null($string) || $string === '') {
			if (isset($timezone)) {
				parent::__construct('now', $timezone);
			} else {
				parent::__construct('now');
			}
			$this->setTimestamp(0);
		} else if ($string instanceof self || $string instanceof parent) {			
			if (isset($timezone)) {
				parent::__construct('now', $timezone);
			} else {
				parent::__construct('now');
			}
			$this->setTimestamp($string->getTimestamp());
		} else if (self::isTimestamp($string)) {
			if (isset($timezone)) {
				parent::__construct('now', $timezone);
			} else {
				parent::__construct('now');
			}
			$this->setTimestamp($string);
		} else {
			try {
				if (isset($timezone)) {
					parent::__construct($string, $timezone);
				} else {
					parent::__construct($string);
				}
			} catch (Exception $ex) {
				throw new Exception('Not able to parse string: "'.$string.'" into EDateTime. '.$ex->getMessage());
				//return new static(strtotime($string));
			}
		}
	}
	
	public function setDate($year, $month, $day) {
		return parent::setDate($year, $month, $day);
	}
	
	public function addSeconds($second) {
		$this->setTimestamp($this->getTimestamp() + $second);
		return $this;
	}
	
	public function addHours($hour) {
		return self::addSeconds($hour * 3600);
	}
	
	public function addDays($day) {
		return self::addHours($day * 24);
	}
	
	public function setTimeAsBeginOfDay() {
		$this->setTime(0, 0, 0);
		return $this;
	}
	
	public function setTimeAsEndOfDay() {
		$this->setTime(23, 59, 59);
		return $this;
	}
	
	public function setAsBeginOfYear() {
		$this->setDate($this->format('y'), 1, 1);
		$this->setTimeAsBeginOfDay();
		return $this;
	}
	
	public function setAsEndOfYear() {
		$this->setDate($this->format('Y'), 12, 31);
		$this->setTimeAsEndOfDay();
		return $this;
	}
	
	public function setTime( $hour, $minute, $second = 0, $microseconds = 0) {
		return parent::setTime($hour, $minute, $second); // Forth parameters is only added/supported by PHP >=7.1
	}
	
	public function mergeTime(DateTime $time) {
		$timestamp = $time->getTimestamp();
		$this->setTime(date('H', $timestamp), date('i', $timestamp), date('s', $timestamp));
		return $this;
	}
	
	public static function isTimestamp($timestamp) {
		return is_numeric($timestamp) && (string) (int) $timestamp === (string) $timestamp;
	}
	
	public static function mergeDateTime(DateTime $date, DateTime $time) {
		$dateFormat = 'Y-m-d';
		$timeFormat = 'H:i:s';
		
		$fullString = $date->format($dateFormat).' '.$time->format($timeFormat);
		$datetime = self::createFromFormat($dateFormat.' '.$timeFormat, $fullString);
		return $datetime;
	}
	
	public static function createFromFormat($format, $time, $object = null) {
		if (isset($object)) {
			$datetime = parent::createFromFormat($format, $time, $object);
		} else {
			$datetime = parent::createFromFormat($format, $time); // Passing null as thrid parameter will cause exception.
		}
		return new EDateTime($datetime->getTimestamp());
	}

	// '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'	=>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
	// '%y Year %m Month %d Day'								=>  1 Year 3 Month 14 Days
	// '%m Month %d Day'								        =>  3 Month 14 Day
	// '%d Day %h Hours'								        =>  14 Day 11 Hours
	// '%d Day'								                    =>  14 Days
	// '%h Hours %i Minute %s Seconds'							=>  11 Hours 49 Minute 36 Seconds
	// '%i Minute %s Seconds'									=>  49 Minute 36 Seconds
	// '%h Hours												=>  11 Hours
	// '%a Days													=>  468 Days
	public static function differentOfHours($startDateTime, $endDateTime) {
		$startDateTime = new self($startDateTime);
		$endDateTime = new self($endDateTime);
		
		//throw new \Exception(abs($startDateTime->getTimestamp() - $endDateTime->getTimestamp()) / 24 / 3600);
		return abs($startDateTime->getTimestamp() - $endDateTime->getTimestamp()) / 3600; 
	}

	public static function different($startDateTime, $endDateTime, $differenceFormat = '%a') {
		$interval = date_diff(new self($startDateTime), new self($endDateTime));
		return $interval->format($differenceFormat);
	}
	
	public function differentInDays($compareTo) {
		$interval = $this->diff(new self($compareTo));
		return $interval->format('%a');
	}
	
	public function inRange($startDate, $startTime = null, $endDate = null, $endTime = null) {
		if (isset($startDate) && isset($endDate)) {
			$start = new self($startDate.' '.$startTime);
			$end = new self($endDate.' '.$endTime);
			if (!isset($endTime)) $end->setTimeAsEndOfDay();
			
			return $this >= $start && $this <= $end;
		} else if (isset($startDate)) {
			$start = new self($startDate.' '.$startTime);
			
			return $this >= $start;
		} else if (isset($endDate)) {
			$end = new self($endDate.' '.$endTime);
			if (!isset($endTime)) $end->setTimeAsEndOfDay();
			
			return $this <= $end;
		} else {
			throw new \Exception('Either start date or end date should be set. ');
		}
		
		
	}
	
	public function setToStringFormat($format) {
		$this->_toStringFormat = $format;
	}
	
	public function getTimestamp() {
		if (method_exists('DateTime', 'getTimestamp')) {
			return parent::getTimestamp();
		} else {
			return $this->format('U');
		}
	}
	
	public function setTimestamp($unixtimestamp){
		if ($unixtimestamp == 0) {
			$this->setDate(0, 0, 0);
			$this->setTime(0, 0, 0);
		} else if(!is_numeric($unixtimestamp) && !is_null($unixtimestamp)) {
			trigger_error('DateTime::setTimestamp() expects parameter 1 to be long, '.gettype($unixtimestamp).' given', E_USER_WARNING);
		} else {
			$this->setDate(date('Y', $unixtimestamp), date('n', $unixtimestamp), date('d', $unixtimestamp));
			$this->setTime(date('G', $unixtimestamp), date('i', $unixtimestamp), date('s', $unixtimestamp));
		}
		return $this;
	}
	
	public function systemFormat() {
		return $this->format(self::FORMAT_MYSQL);
	}

	public function dbFormat() {
		return $this->format(self::FORMAT_MYSQL);
	}
	
	public function format($format) {
		$timezone = $this->getTimezone();
		//$this->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'));
		if (parent::format('U') <= 0) return null;
		$return = parent::format($format);
		//$this->setTimezone($timezone);
		return $return;
	}
	
	public function relativeTimeInWords($dateTime, $tier = 2) {
		$relative = new DateTime($dateTime);
		$diff = $this->diff($relative);
		
		$keys = [
			'y' => 'year', 
			'm' => 'month', 
			'd' => 'day', 
			'h' => 'hour', 
			'i' => 'minute', 
			's' => 'second'
		];
		
		$words = [];
		foreach ($keys as $index => $word) {
			if ($diff->{$index} && $tier) {
				$tier--;
				$plural = $diff->{$index} > 1 ? '(s)' : '';
				$words[] = $diff->{$index} . ' ' . $word . $plural;
			}
		}
		return implode(', ', $words);
	}
	
	public function toString($dateFormatType = 'medium', $timeFormatType = 'short') {
		if ($this->getTimestamp() <= 0) return '';
		if (isset($this->_toStringFormat)) {
			return date($this->_toStringFormat, $this->getTimestamp());
		}
		return $this->format('Y-m-d H:i:s');
		return Yii::$app->formatter->asDateTime($this->getTimestamp());
	}
	
	public function __toString() {
		return $this->toString();
	}
	
	public function cloneIt() {
		$cloned = new self();
		$cloned->setTimestamp($this->getTimestamp());
		return $cloned;
	}
	
	/*
	public static function getLocalDateTimeFormat() {
		$config = Yii::app()->getParams();
		return $config['input_datetime_format'];
	}*/
}
