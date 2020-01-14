<?php
namespace ant\helpers;

class Html extends \yii\helpers\Html {	
	public static function removeEmptyParagraph($str) {
		$str = str_replace('<div dir="auto">&nbsp;</div>', '', $str);
		return preg_replace("/<p[^>]*>[\s|&nbsp;]*<\/p>/", '', $str);
	}
	
	public static function removeEmptyTag($str) {
		return preg_replace("/<[a-z]+[^>]*>[\s|&nbsp;]*<\/[a-z]+>/i", '', $str);
	}
	
	public static function clean($str) {
		$str = self::removeEmptyParagraph($str);
		$str = self::removeEmptyTag($str);
		$str = preg_replace("/<div dir=\"auto\">[\s\n\r]*(.*)[\s\n\r]*<\/div>/", '<p>$1</p>', $str);
		return $str;
	}
}