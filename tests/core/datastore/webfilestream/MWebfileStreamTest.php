<?php

use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;

/**
 * Test class for MWebfileStreamTest.
 */
class MWebfileStreamTest extends PHPUnit_Framework_TestCase {
    /**
     * @var MDatastoreFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MWebfileStream(null);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream::getXML
     */
    public function testGetXML()
    {
        echo "bla";
    }
    
    /**
     * @covers simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream::getWebfiles
     */
    public function testGetWebfiles() {
        echo "bla";
    }
    
}
