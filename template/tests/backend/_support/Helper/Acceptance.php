<?php
namespace tests\backend\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
	protected $initBaseUrl;
	
	public function _before($test) {
		$this->initBaseUrl = $this->getModule('WebDriver')->_getUrl();
		//throw new \Exception($this->baseUrl);
	}
	
	public function getBaseUrl() {
		return $this->initBaseUrl;
	}
	
	public function setBaseUrl($url) {
		$this->getModule('WebDriver')->_reconfigure(['url' => $url]);
	}
	
	public function resetUrl() {
		$this->setBaseUrl($this->initBaseUrl);
	}

}

