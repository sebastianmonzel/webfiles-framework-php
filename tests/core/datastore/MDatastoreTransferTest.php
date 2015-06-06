<?php

use simpleserv\webfilesframework\core\datastore\MDatastoreFactory;
use simpleserv\webfilesframework\core\datastore\MDatastoreTransfer;
use simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;

/**
 * Test class for MDatastoreTransfer.
 */
class MDatastoreTransferTest extends PHPUnit_Framework_TestCase {
    /**
     * @var MDatastoreTransfer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
    	
    	//new MDirectoryDatastore(new MDirectory("."));
    	
        //$this->object = new MDatastoreTransfer($source, $target);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers simpleserv\webfilesframework\core\datastore\MDatastoreTransfer::transfer
     */
    public function testTransfer() {
    	
        echo "bla";
    }
}
