<?php
namespace ant\behaviors;

use Yii;
use ant\helpers\DateTime;

class DateTimeAttributeQueryBehavior extends \yii\base\Behavior {
	public function andWhereYear($condition) {
		$condition[1] = 'YEAR('.$condition[1].')';
		
		return $this->owner->andWhere($condition);
	}
	
	public function getDateTimeNewerAgoCondition($attribute, $interval, $unit = 'seconds') {
		return ['>', $attribute , date('Y-m-d H:i:s', strtotime("- ". $interval." ".$unit) ) ];
	}
	
	public function andWhereNotPast($attribute) {
		return $this->andWhereNewerDaysAgo($attribute, 0);
	}
	
	public function andWherePast($attribute) {
		return $this->andWhereOlderDaysAgo($attribute, 0);
	}
	
	public function andWhereOlderThanDate($attribute, $date = null) {
		if (isset($date)) {
			return $this->andWhereOlderThan($attribute, (new DateTime($date))->setTimeAsEndOfDay());
		} else {
			return $this->owner;
		}
	}
	
	public function andWhereNewerThanDate($attribute, $date = null) {
		if (isset($date)) {
			return $this->andWhereNewerThan($attribute, (new DateTime($date))->setTimeAsBeginOfDay());
		} else {
			return $this->owner;
		}
	}
	
	// @TODO: Seem like have bug, it select record which date is not between fromDateTime and toDateTime
	public function andWhereBetween($attribute, $fromDateTime, $toDateTime) {
		return $this->andWhereNewerThan($fromDateTime)
			->andWhereOlderThan($toDateTime);
	}
	
	public function andWhereOlderThan($attribute, $dateTime = null) {
		if (isset($dateTime)) {
			return $this->owner->andWhere(['<', $attribute , (new DateTime($dateTime))->format(DateTime::FORMAT_MYSQL) ]);
		} else {
			return $this->owner;
		}
	}
	
	public function andWhereNewerThan($attribute, $dateTime = null) {
		if (isset($dateTime)) {
			return $this->owner->andWhere(['>', $attribute , (new DateTime($dateTime))->format(DateTime::FORMAT_MYSQL) ]);
		} else {
			return $this->owner;
		}
	}
	
    public function andWhereOlderDaysAgo($attribute, $dayPast) {
        return $this->owner->andWhere(['<', $attribute , date('Y-m-d H:i:s', strtotime("- ". $dayPast." day") ) ]);
    }
	
    public function andWhereNewerDaysAgo($attribute, $dayPast) {
        return $this->owner->andWhere(['>', $attribute , date('Y-m-d H:i:s', strtotime("- ". $dayPast." day") ) ]);
    }
	
	public function andWhereOlderThanNow($attribute) {
		return $this->andWhereOlderDaysAgo($attribute, 0);
	}
	
	public function andWhereNewerThanNow($attribute) {
		return $this->andWhereNewerDaysAgo($attribute, 0);
	}
}
