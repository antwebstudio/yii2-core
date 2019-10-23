<?php
namespace ant\behaviors;

class DateTimeRangeBehavior extends \kartik\daterange\DateRangeBehavior {
    protected static function dateToTime($date)
    {
        return $date;
    }
}