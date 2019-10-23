<?php 

namespace helpers;

use UnitTester;
use ant\helpers\Currency;

class CurrencyCest
{
    public function _before(UnitTester $I)
    {
    }
	
	public function testRounding(UnitTester $I) {
		$tests = [
			[10.53, 1, 10.50],
			[10.53, 2, 10.53],
			[10.53, 0, 11],
			[10.50, 1, 10.50],
			[10.50, 2, 10.50],
			[10.50, 0, 11],
			[10.00, 1, 10.00],
			[10.00, 2, 10.00],
			[10.00, 0, 10.00],
		];
		
		$results = [];
		$expectedArr = [];
		foreach($tests as $test) {
			list($test, $decimal, $expected) = $test;
			$results[] = Currency::rounding($test, $decimal);
			$expectedArr[] = $expected;
		}
		
		$I->assertEquals($expectedArr, $results);
	}

    // tests
    public function testRoundUp(UnitTester $I)
    {
		$tests = [
			[10.53, 1, 10.60],
			[10.53, 2, 10.53],
			[10.53, 0, 11],
			[10.50, 1, 10.50],
			[10.50, 2, 10.50],
			[10.50, 0, 11],
			[10.00, 1, 10.00],
			[10.00, 2, 10.00],
			[10.00, 0, 10.00],
		];
		
		$results = [];
		$expectedArr = [];
		foreach($tests as $test) {
			list($test, $decimal, $expected) = $test;
			$results[] = Currency::roundUp($test, $decimal);
			$expectedArr[] = $expected;
		}
		
		$I->assertEquals($expectedArr, $results);
    }
}
