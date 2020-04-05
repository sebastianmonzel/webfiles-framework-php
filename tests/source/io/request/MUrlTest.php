<?php

use PHPUnit\Framework\TestCase;
use webfilesframework\io\request\MUrl;

/**
 * @covers webfilesframework\io\request\MUrl
 */
class MUrlTest extends TestCase {

    protected $testUrl;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        $this->testUrl = new MUrl("http://www.simpleserv.de/?paramone=foo&paramtoo=bar&");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() : void
    {
    }

    /**
     * @covers webfilesframework\io\request\MUrl::getParam
     */
    public function testGetParam() {
    	
        $this->assertEquals('foo',$this->testUrl->getParam('paramone'));
        $this->assertEquals('bar',$this->testUrl->getParam('paramtoo'));
    }
    
    /**
     * @covers webfilesframework\io\request\MUrl::getQueryString
     */
    public function testGetQueryString() {
    	
    	$this->assertEquals('paramone=foo&paramtoo=bar&',$this->testUrl->getQueryString());
    }
}
