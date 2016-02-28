<?php

use simpleserv\webfilesframework\core\io\request\MUrl;

/**
 * Test class for MDatastoreFactory.
 */
class MUrlTest extends PHPUnit_Framework_TestCase {
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
        $this->object = new MUrl("http://www.simpleserv.de/?paramone=foo&paramtoo=bar&");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers simpleserv\webfilesframework\core\io\request\MUrl::getParam
     */
    public function testGetParam() {
    	
        $this->assertEquals('foo',$this->object->getParam('paramone'));
        $this->assertEquals('bar',$this->object->getParam('paramtoo'));
    }
    
    /**
     * @covers simpleserv\webfilesframework\core\io\request\MUrl::getQueryString
     */
    public function testGetQueryString() {
    	
    	$this->assertEquals('paramone=foo&paramtoo=bar&',$this->object->getQueryString());
    }
}
