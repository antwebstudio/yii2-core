<?php

namespace ant\helpers;

class File {
	const TYPE_IMAGE = 'image';
	const TYPE_VIDEO = 'video';
	
	protected $_path;
	protected static $_mimeGroup = [
		self::TYPE_IMAGE => ['image/*'],
		self::TYPE_VIDEO => ['video/*'],
	];
	
	public static function isVideoTypeMime($fileMimeType) {
		//return in_array($fileMimeType, self::$_mimeGroup[self::TYPE_VIDEO]);
		return self::match(self::$_mimeGroup[self::TYPE_VIDEO], $fileMimeType);
	}
	
	public static function isImageTypeMime($fileMimeType) {
		//return in_array($fileMimeType, self::$_mimeGroup[self::TYPE_IMAGE]);
		return self::match(self::$_mimeGroup[self::TYPE_IMAGE], $fileMimeType);
	}
	
	public static function getTypeByMime($mime) {
		foreach (self::$_mimeGroup as $type => $mimeGroup) {
			if (in_array($mime, $mimeGroup)) {
				return $type;
			}
		}
	}
	
	public static function storeArray($fullPath, $array) {
		file_put_contents($fullPath, '<?php return '.var_export($array, 1).';');
	}
	
	public static function loadArray($fullPath, $errorIfFileNotExist = false) {
		if (file_exists($fullPath)) {
			return require $fullPath;
		}
	}
	
	public static function createFromPath($path) {
		if (!isset($path) || $path == '') throw new \Exception('Cannot create from empty path. ');
		
		$file = new self;
		$file->_path = $path;
		return $file;
	}
	
	protected static function match($maskes, $match) {
		foreach ($maskes as $mask) {
			if (strcasecmp($mask, $match) === 0) {
                return true;
            }

            if (strpos($mask, '*') !== false && preg_match(self::buildMimeTypeRegEx($mask), $match)) {
                return true;
            }
		}
		return false;
	}
	
	protected static function buildMimeTypeRegEx($mask) {
		return '/^' . str_replace('\*', '.*', preg_quote($mask, '/')) . '$/i';
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