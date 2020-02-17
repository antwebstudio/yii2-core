<?php
namespace tests\backend\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{
    public function _before(\Codeception\TestInterface $test)
    {
        parent::_before($test);

        /** @var Yii2 $yii2 */
		
        $yii2 = $this->getModule('Yii2');
        
        $yii2->client->setServerParameters([
            'REMOTE_ADDR' => '127.0.0.1',
            'SCRIPT_FILENAME' => '@frontend/web/index.php',
            'SCRIPT_NAME' => '/index.php'
        ]);
    }
	
	public function sendRequest($uri, $params, $files) {
		return $this->getModule('Yii2')->_loadPage('POST', $uri, $params, $files);
	}

}
