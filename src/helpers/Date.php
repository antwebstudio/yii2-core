<?php
namespace ant\helpers;

class Date {
	protected $_date;
	
	public function __construct($date = null) {
		if ($date instanceof Date) {
			$this->_date = $date->systemFormat();
		} else if (isset($date)) {
			$this->_date = date('Y-m-d', strtotime($date));
		} else {
			$this->_date = date('Y-m-d');
		}
	}
	
	public function cloneIt() {
		return new self($this->_date);
	}
	
	/*public function cloneIt() {
		return new self($this->_date);
	}*/
	
	public function addDays($days) {
		$operator = $days > 0 ? '+' : ''; // If negative, itself already have negative sign, hence no need to append
		
		$this->_date = date('Y-m-d', strtotime($this->_date.' '.$operator. $days.' days'));
		return $this;
	}
	
	public function systemFormat() {
		return $this->format('Y-m-d');
	}
	
	public function format($format = 'Y-m-d') {
		return date($format, strtotime($this->_date));
	}
	
	public function toString() {
		return $this->format();
	}
	
	public function getTimestamp() {
		return strtotime($this->_date);
	}
	
	public function __toString() {
		return $this->toString();
	}
	
	public function compare($date) {
		$date = new self($date);
		
		if ($date->getTimestamp() == $this->getTimestamp()) {
			return 0;
		}
		
		return $this->getTimestamp() > $date->getTimestamp() ? 1 : -1;
	}
	
	// bool overlap = a.start < b.end && b.start < a.end;
	// Reference: https://stackoverflow.com/questions/13513932/algorithm-to-detect-overlapping-periods
	public static function intersect($range1, $range2, $includedEqual = true) {
		if (is_array($range1)) {
			$startDate1 = new self($range1[0]);
			$endDate1 = new self($range1[1]);
		} else {
			$startDate1 = new self($range1);
			$endDate1 = new self($range1);
		}
		if (is_array($range2)) {
			$startDate2 = new self($range2[0]);
			$endDate2 = new self($range2[1]);
		} else {
			$startDate2 = new self($range2);
			$endDate2 = new self($range2);
		}
		
		return $startDate1->compare($endDate2) != 1 && $startDate2->compare($endDate1) != 1;
	}
	
	public static function split(array $range, $splitAt, $includeEqual = false) {
		$startDate = new self($range[0]);
		$endDate = new self($range[1]);
		$splitAt = new self($splitAt);
		
		$part1 = null;
		$part2 = null;
		
		if ($startDate->compare($splitAt) == -1 && $endDate->compare($splitAt) == 1) {
		// $splitAt in the middle of the range
			$part1 = [$startDate->systemFormat(), $splitAt->cloneIt()->addDays(-1)->systemFormat()];
			$part2 = [$splitAt->cloneIt()->addDays(1)->systemFormat(), $endDate->systemFormat()];
		} else if ($startDate->compare($splitAt) == 0 && $endDate->compare($splitAt) == 1) {
		// $splitAt in the beginning of the range
			$part2 = [$splitAt->cloneIt()->addDays(1)->systemFormat(), $endDate->systemFormat()];
		} else if ($startDate->compare($splitAt) == -1 && $endDate->compare($splitAt) == 0) {
		// $splitAt in the endding of the range
			$part1 = [$startDate->systemFormat(), $splitAt->cloneIt()->addDays(-1)->systemFormat()];
		}
		return [$part1, $part2];
	}
}