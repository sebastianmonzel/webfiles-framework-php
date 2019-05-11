<?php

use webfilesframework\core\datasystem\file\system\MDirectory;
use webfilesframework\core\datasystem\file\system\MFile;

/**
 * @covers webfilesframework\core\datasystem\file\system\MDirectory
 * @covers webfilesframework\core\datasystem\file\system\MFile
 */
class MDirectoryTest extends PHPUnit_Framework_TestCase {
    /**
     * @var MDatastoreFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new MDirectory(__DIR__);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }
    
    /**
     * @covers webfilesframework\core\datasystem\file\system\MDirectory::getFiles
     */
    public function testGetFiles() {
    	
    	$givenFiles = $this->object->getFiles();
    	
    	$referenceFile = new MFile(__DIR__ . '/MDirectoryTest.php');
    	$referenceFiles = array();
    	$referenceFiles[] = $referenceFile;
    	
    	$this->assertEquals($referenceFiles,$givenFiles);
    }

}
