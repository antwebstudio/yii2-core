<?php
namespace ant\components;

class Theme extends \yii\base\Theme {
	public $asset;
	public $skin;
	
	protected $_asset;
	
	public function init() {
		if (isset($this->asset)) {
			$assetClass = $this->asset;
			$this->_asset = new $assetClass;
			$this->_asset->publish(\Yii::$app->assetManager);
			$this->setBaseUrl($this->_asset->baseUrl);
			$this->setBasePath($this->_asset->basePath);
		}
		return parent::init();
	}
	
	public function registerAsset($view) {
		$this->_asset->register($view);
	}
}