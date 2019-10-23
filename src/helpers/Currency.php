<?php
namespace ant\helpers;

class Currency {
    public static function rounding($value, $decimal = 2) {
		if (isset($value)) {
			if (!is_numeric($value)) throw new \Exception('Numeric value expected. (Value: '.$value.')');
			return number_format($value, $decimal, '.', '');
		}
    }
	
	public static function roundUp($value, $decimal = 0) {
		if (!is_numeric($value)) throw new \Exception('Numeric value expected. (Value: '.$value.')');
		
		if ($decimal < 0) { $decimal = 0; }
		$mult = pow(10, $decimal);
		return ceil($value * $mult) / $mult;
	}
	
	public static function getIntegerPart($price) {
		if (($pos = strpos($price, '.')) !== false) {
			return substr($price, 0, $pos);
		}
	}
	
	public static function getDecimalPart($price) {
		if (($pos = strpos($price, '.')) !== false) {
			return substr($price, $pos + 1);
		}
	}
	
	public static function getCurrentSymbol() {
		return trim(str_replace('0.00', '', \Yii::$app->formatter->asCurrency(0)));
	}
	
	public static function getSymbol($currencyCode) {
		switch ($currencyCode) {
			case 'MYR':
				return 'RM';
			case 'SGD':
				return 'SGD';
			case 'USD':
				return '$';
		}
	}
}
