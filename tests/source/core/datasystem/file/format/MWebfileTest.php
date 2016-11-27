<?php

use simpleserv\webfilesframework\core\datastore\MDatastoreFactory;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Test class for MWebfileTest.
 */
class MWebfileTest extends PHPUnit_Framework_TestCase {
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
        $this->object = new MWebfile();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers simpleserv\webfilesframework\core\datasystem\file\format\MWebfile::getSimplifiedAttributeName
     */
    public function testGetSimplifiedAttributeName() {
    	
    	$calculatedSimplifiedAttributename = MWebfile::getSimplifiedAttributeName("m_sName");
    	$referencedSimplifiedAttributename = "name";
    	$this->assertEquals($referencedSimplifiedAttributename,$calculatedSimplifiedAttributename);
    }

    public function testMarshallWebfile() {
        $sample = new \simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile();
        $marshalledWebfile = $sample->marshall(true);
        //self::assertXmlFileEqualsXmlFile();

    }

}
