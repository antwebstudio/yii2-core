<?php

namespace ant\helpers;

class File {
	const TYPE_IMAGE = 'image';
	const TYPE_VIDEO = 'video';
	
	protected $_path;
	protected static $_mimeGroup = [
		self::TYPE_IMAGE => ['image/jpeg', 'image/gif', 'image/png'],
		self::TYPE_VIDEO => ['video/mp4'],
	];
	
	public static function isVideoTypeMime($mime) {
		return in_array($mime, self::$_mimeGroup[self::TYPE_VIDEO]);
	}
	
	public static function isImageTypeMime($mime) {
		return in_array($mime, self::$_mimeGroup[self::TYPE_IMAGE]);
	}
	
	public static function getTypeByMime($mime) {
		foreach (self::$_mimeGroup as $type => $mimeGroup) {
			if (in_array($mime, $mimeGroup)) {
				return $type;
			}
		}
	}
	
	public static function createFromPath($path) {
		if (!isset($path) || $path == '') throw new \Exception('Cannot create from empty path. ');
		
		$file = new self;
		$file->_path = $path;
		return $file;
	}
	
	public function setFilename($newFilename, $includedExt = false) {
		$pathinfo = pathinfo($this->_path);
		$this->_path = $pathinfo['dirname'].'/'.$newFilename.'.'.$pathinfo['extension'];
	}
	
	public function getFilename($includedExt = false) {
		$pathinfo = pathinfo($this->_path);
		return $pathinfo['filename'];
	}
	
	public function getPath() {
		return $this->_path;
	}
}