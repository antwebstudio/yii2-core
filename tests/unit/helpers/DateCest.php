<?php
use ant\helpers\Date;

class DateCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testAddDays(UnitTester $I)
    {
		// Positive
		$date = new Date('2017-10-18');
		$I->assertEquals('2017-10-20', $date->addDays(2)->format('Y-m-d'));
		
		// Negative
		$date = new Date('2017-10-18');
		$I->assertEquals('2017-10-16', $date->addDays(-2)->format('Y-m-d'));
		
		// Positive Over Month
		$date = new Date('2017-10-30');
		$I->assertEquals('2017-11-01', $date->addDays(2)->format('Y-m-d'));
		
		// Negative Over Month
		$date = new Date('2017-10-2');
		$I->assertEquals('2017-09-30', $date->addDays(-2)->format('Y-m-d'));
    }
	
	public function testClone(UnitTester $I) {
		$date = new Date('2017-10-18');
		$cloned = $date->cloneIt()->addDays(2);
		
		$I->assertEquals('2017-10-18', $date->format('Y-m-d'));
		$I->assertEquals('2017-10-20', $cloned->format('Y-m-d'));
	}
	
	public function testCompare(UnitTester $I) {
		$date = new Date('2017-10-18');
		$date2 = new Date('2017-10-20');
		$date3 = new Date('2017-10-18');
		
		$I->assertEquals(0, $date->compare($date3));
		$I->assertEquals(0, $date3->compare($date));
		$I->assertEquals(1, $date2->compare($date));
		$I->assertEquals(-1, $date->compare($date2));
	}
	
	public function testIntersect(UnitTester $I) {
		$I->assertTrue(Date::intersect(['2017-10-18', '2017-10-22'], '2017-10-20'));
		$I->assertTrue(Date::intersect('2017-10-20', ['2017-10-18', '2017-10-22']));
		
		// |--------|
		//      |---------|
		$I->assertTrue(Date::intersect(['2017-10-18', '2017-10-22'], ['2017-10-20', '2017-10-24']));
		
		//            |--------|
		//      |---------|
		$I->assertTrue(Date::intersect(['2017-10-22', '2017-10-26'], ['2017-10-20', '2017-10-24']));
		
		// |----------------|
		//     |---------|
		$I->assertTrue(Date::intersect(['2017-10-20', '2017-10-26'], ['2017-10-22', '2017-10-24']));
		
		//     |---------|
		// |----------------|
		$I->assertTrue(Date::intersect(['2017-10-22', '2017-10-24'], ['2017-10-20', '2017-10-26']));
		
		// |---------|
		//             |---------|
		$I->assertFalse(Date::intersect(['2017-10-18', '2017-10-20'], ['2017-10-21', '2017-10-24']));
		
		//             |---------|
		// |---------|
		$I->assertFalse(Date::intersect(['2017-10-21', '2017-10-24'], ['2017-10-18', '2017-10-20']));
	}
	
	public function testSplit(UnitTester $I) {
		// Single date split
		
		// Split at middle
		$I->assertEquals([['2017-10-18', '2017-10-19'], ['2017-10-21', '2017-10-22']], Date::split(['2017-10-18', '2017-10-22'], '2017-10-20'));
		
		// Split at beginning
		$I->assertEquals([null, ['2017-10-19', '2017-10-22']], Date::split(['2017-10-18', '2017-10-22'], '2017-10-18'));
		
		// Split at endding
		$I->assertEquals([['2017-10-18', '2017-10-21'], null], Date::split(['2017-10-18', '2017-10-22'], '2017-10-22'));
		
		// Split for same date
		$I->assertEquals([null, null], Date::split(['2017-10-18', '2017-10-18'], '2017-10-18'));
	}
}
