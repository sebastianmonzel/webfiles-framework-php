<?php

use PHPUnit\Framework\TestCase;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;


/**
 * @covers webfilesframework\core\datasystem\file\format\MWebfileStream
 */
class MWebfileStreamTest extends TestCase {
    /**
     * @var MDatastoreFactory
     */
    protected $object;

    public static $webfileStreamAsStringReference = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><webfilestream><webfiles><object classname=\"webfilesframework\\core\\datastore\\types\\database\\MSampleWebfile\">
	<firstname><![CDATA[Hello]]></firstname>
	<lastname><![CDATA[World]]></lastname>
	<street><![CDATA[Blumenstrasse]]></street>
	<housenumber><![CDATA[4]]></housenumber>
	<postcode><![CDATA[67433]]></postcode>
	<city><![CDATA[Neustadt an der Weinstrasse]]></city>
	<id><![CDATA[1]]></id>
	<time><![CDATA[4711]]></time>
</object><object classname=\"webfilesframework\\core\\datastore\\types\\database\\MSampleWebfile\">
	<firstname><![CDATA[Sergey]]></firstname>
	<lastname><![CDATA[Brin]]></lastname>
	<street><![CDATA[Blumenstraße]]></street>
	<housenumber><![CDATA[8]]></housenumber>
	<postcode><![CDATA[67433]]></postcode>
	<city><![CDATA[Neustadt an der Weinstraße]]></city>
	<id><![CDATA[2]]></id>
	<time><![CDATA[4712]]></time>
</object></webfiles></webfilestream>";

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        $this->object = new MWebfileStream(null);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() : void
    {
    }

    public function testGetXML()
    {
        $webfileStream = new MWebfileStream(static::$webfileStreamAsStringReference);
        $remarshalledXml = $webfileStream->getXML();

        $this->assertXmlStringEqualsXmlString(
            static::$webfileStreamAsStringReference,
            $remarshalledXml
        );
    }
    
    /**
     * @covers webfilesframework\core\datasystem\file\format\MWebfileStream::getWebfiles
     */
    public function testGetWebfilesOnStringInput() {

        $webfileStream = new MWebfileStream(static::$webfileStreamAsStringReference);
        $webfilesFromStream = $webfileStream->getWebfiles();

        $this->assertEquals(2,count($webfilesFromStream));
    }

    public function testInstantiationWithMalformedStringThrowsException() {

    	self::expectException("\webfilesframework\MWebfilesFrameworkException");
    	self::expectExceptionMessage("Error: test that it not works");

        $webfileStream = new MWebfileStream("Error: test that it not works");

    }

    public function testInstantiationWithWrongXmlThrowsException() {

	    self::expectException("\webfilesframework\MWebfilesFrameworkException");
	    self::expectExceptionMessage("No webfiles child exists on root element.");

        $webfileStream = new MWebfileStream("<test>
<wrong>
<value>test</value>
<value>test</value>
</wrong>
</test>");

    }

    public function testInstantiationWithWrongArrayArgumentThrowsException() {

	    self::expectException("\webfilesframework\MWebfilesFrameworkException");
	    self::expectExceptionMessage("Not all elements in array are from type MWebfile.");

        $webfiles = array();
        $webfiles[0] = new MSampleWebfile();
        $webfiles[1] = "test";

        $webfileStream = new MWebfileStream($webfiles);

    }

    
}
