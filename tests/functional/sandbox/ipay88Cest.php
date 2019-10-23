<?php
namespace tests\codeception\frontend\sandbox\sandbox;
use tests\codeception\frontend\FunctionalTester;

class ipay88Cest
{
	protected $validSignature = 'wJxG41c88eddUT/xCTI/BmDtyaw=';
	protected $validMerchantCode = 'sandbox';
	protected $validMerchantKey = 'inspiren';
	
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }
	
	public function testInvalidMerchantCode(FunctionalTester $I)
    {
		$params = [
			'MerchantCode' => 'M0000',
			'Signature' => $this->createSignature($this->validMerchantCode, $this->validMerchantKey, '1', '1.00', 'MYR'),
			'Amount' => '1.00',
			'Currency' => 'MYR',
			'RefNo' => '1',
		];
		
		$I->sendRequest('POST', ['sandbox', 'sandbox' => 'ipay88'], $params);
		
		$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
		$I->dontSeeElement('[type=submit]');

    }
	
	public function testSignatureNotMatch(FunctionalTester $I)
    {
		$params = [
			'MerchantCode' => $this->validMerchantCode,
			'Signature' => 'invalid_signature',
			'Amount' => '1.00',
			'Currency' => 'MYR',
			'RefNo' => '1',
		];
		
		$I->sendRequest('POST', ['sandbox', 'sandbox' => 'ipay88'], $params);
		
		$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
		$I->dontSeeElement('[type=submit]');

    }
	
	public function testSuccess(FunctionalTester $I)
    {
		$params = [
			'MerchantCode' => $this->validMerchantCode,
			'Signature' => $this->createSignature($this->validMerchantCode, $this->validMerchantKey, '1', '1.00', 'MYR'),
			'Amount' => '1.00',
			'Currency' => 'MYR',
			'RefNo' => '1',
			'UserName' => 'Test User',
		];
		
		$I->sendRequest('POST', ['sandbox', 'sandbox' => 'ipay88'], $params);
		
		$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
		$I->seeElement('[type=submit]');

    }
	

	protected function createSignature($merchantCode, $merchantKey, $refNo, $total, $currency) {
		$string = $merchantKey . $merchantCode . $refNo . str_replace(".", "", $total) . $currency;
		return $this->createSignatureFromString($string);
	}
	
	protected function createSignatureFromString($fullStringToHash)
    {
        return base64_encode($this->hex2bin(sha1($fullStringToHash)));
    }

    private function hex2bin($hexSource)
    {
        $bin = '';
        for ($i = 0; $i < strlen($hexSource); $i = $i + 2) {
            $bin .= chr(hexdec(substr($hexSource, $i, 2)));
        }
        return $bin;
    }
}
