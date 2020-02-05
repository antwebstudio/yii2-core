<?php
namespace ant\actions;

use yii\web\Response;

class ThumbOnFlyAction extends \yii\base\Action {
	public $url;
	
	public function run($url, $width = null, $height = null, $position = null) {
		$img = \Intervention\Image\ImageManagerStatic::make($url);
		
		if ($width && $height) {
		} else if ($width) {
			$height = (int) ($img->height() / $img->width() * $width);
		} else if ($height) {
			$width = (int) ($img->width() / $img->height() * $height);
		}
		
		if ($width && $height) $img->fit($width, $height, null, $position);
		
		$response = \Yii::$app->getResponse();
		$response->headers->set('Content-Type', $img->mime);
		$response->format = Response::FORMAT_RAW;
		
		return $img->stream();
	}
}