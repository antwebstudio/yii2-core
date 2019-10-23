<?php
use ant\helpers\DateTime;

class DateTimeCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testInRangeForStart(UnitTester $I)
    {
		$now = new DateTime('2019-04-23 10:00:00');
		$later = $now->cloneIt()->addSeconds(1800);
		$before = $now->cloneIt()->addSeconds(-1800);
		
		// Later
		
		// Date only
		$I->assertTrue($now->inRange($later->format('Y-m-d')));
		
		// Date and time only
		$I->assertFalse($now->inRange($later->format('Y-m-d'), $later->format('H:i:s')));
		
		// Before
		
		// Date only
		$I->assertTrue($now->inRange($before->format('Y-m-d')));
		
		// Date and time only
		$I->assertTrue($now->inRange($before->format('Y-m-d'), $before->format('H:i:s')));
    }
	
    public function testInRangeForEnd(UnitTester $I)
    {
		$now = new DateTime('2019-04-23 10:00:00');
		$later = $now->cloneIt()->addSeconds(1800);
		$before = $now->cloneIt()->addSeconds(-1800);
		
		// Later
		
		// Date only
		$I->assertTrue($now->inRange(null, null, $later->format('Y-m-d')));
		
		// Date and time only
		$I->assertTrue($now->inRange(null, null, $later->format('Y-m-d'), $later->format('H:i:s')));
		
		// Before
		
		// Date only
		$I->assertTrue($now->inRange(null, null, $before->format('Y-m-d')));
		
		// Date and time only
		$I->assertFalse($now->inRange(null, null, $before->format('Y-m-d'), $before->format('H:i:s')));
    }
	
    public function testInRangeForStartAndEnd(UnitTester $I)
    {
		$now = new DateTime('2019-04-23 10:00:00');
		$later = $now->cloneIt()->addSeconds(1800);
		$later2 = $now->cloneIt()->addSeconds(3600);
		$before = $now->cloneIt()->addSeconds(-1800);
		$before2 = $now->cloneIt()->addSeconds(-3600);
		
		// Between
		$I->assertTrue($now->inRange($before->format('Y-m-d'), null, $later->format('Y-m-d'), null));
		$I->assertTrue($now->inRange($before->format('Y-m-d'), $before->format('H:i:s'), $later->format('Y-m-d'), $later->format('H:i:s')));
		
		// Start and end before
		$I->assertFalse($now->inRange($before2->format('Y-m-d'), $before2->format('H:i:s'), $before->format('Y-m-d'), $before->format('H:i:s')));
		
		// Start and end after
		$I->assertFalse($now->inRange($later->format('Y-m-d'), $later->format('H:i:s'), $later2->format('Y-m-d'), $later2->format('H:i:s')));
    }
}
