<?php
use ant\helpers\File;

class FileCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testSetFilename(UnitTester $I)
    {
		$path = '1/myfile.txt';
		$newFilename = 'newfile';
		$expected = '1/'.$newFilename.'.txt';
		
		$file = File::createFromPath($path);
		$file->setFilename($newFilename);
		
		$I->assertEquals($expected, $file->getPath());
    }
	
	public function testGetFilename(UnitTester $I) {
		$expected = 'myfile';
		$path = '1/'.$expected.'.txt';
		
		$file = File::createFromPath($path);
		
		$I->assertEquals($expected, $file->getFilename(false));
	}
}
