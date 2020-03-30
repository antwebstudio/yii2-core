<?php
namespace ant\actions;

use yii\web\Response;
use yii\helpers\FileHelper;

class ThumbOnFlyAction extends \yii\base\Action {
	public $url;
	
	public function run($url, $width = null, $height = null, $fitType = 'fit', $position = null) {
		$img = \ant\file\models\FileAttachment::generateThumbnail($url, $width, $height, $fitType, $position);
		
		if (!is_object($img)) $img = \Intervention\Image\ImageManagerStatic::make($img);
		
		$response = \Yii::$app->getResponse();
		$response->headers->set('Content-Type', $img->mime);
		$response->format = Response::FORMAT_RAW;
		
		
		return $img->stream();
	}
}