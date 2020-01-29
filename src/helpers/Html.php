<?php
namespace ant\helpers;

class Html extends \yii\helpers\Html {	
	public static function removeEmptyParagraph($str) {
		if (YII_DEBUG) throw new \Exception('DEPRECATED');

		//$str = str_replace('<div dir="auto">&nbsp;</div>', '', $str);
		return preg_replace("/<p[^>]*>[\s|&nbsp;]*<\/p>/", '', $str);
	}
	
	public static function removeEmptyTag($str, $tags = ['div', 'span', 'p']) {
		foreach ($tags as $tag) {
			$str = preg_replace("/<".$tag."[^>]*>[\s|&nbsp;]*<\/".$tag.">/", '', $str);
		}
		return $str;
	}
	
	public static function normalizeParagraph($str) {
		$str = preg_replace("/<div dir=\"auto\">[\s\n\r]*(.*)[\s\n\r]*<\/div>/", '<p>$1</p>', $str);
		return $str;
	}
	
	public static function clean($str) {
		return self::normalizeParagraph(self::removeEmptyTag($str));
	}
	
	public static function video($url, $options = []) {
		return self::tag('video', self::tag('source', null, ['src' => $url]), $options);
	}
}