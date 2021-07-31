<?php

namespace test\webfilesframework\core\datasystem\file\format;

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;


/**
 * @covers webfilesframework\core\datasystem\file\format\MWebfileStream
 */
class MWebfileStreamTest extends MAbstractWebfilesFramworkTest {

    /**
     * @var MWebfileStream
     */
    protected $webfileStream;

    public static $webfileStreamAsXmlStringReference = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><webfilestream><webfiles><object classname=\"webfilesframework\\core\\datastore\\types\\database\\MSampleWebfile\">
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

    public static $webfileStreamAsJsonStringReference = "[
        {
        \"classname\": \"webfilesframework\\\\core\\\\datastore\\\\types\\\\database\\\\MSampleWebfile\",
        \"webfile\": {
            \"firstname\": \"Hello\",
            \"lastname\": \"World\",
            \"street\": \"Blumenstrasse\",
            \"housenumber\": \"4\",
            \"postcode\": \"67433\",
            \"city\": \"Neustadt an der Weinstrasse\",
            \"id\": \"1\",
            \"time\": \"4711\"
            }
        }
        ,
        {
            \"classname\": \"webfilesframework\\\\core\\\\datastore\\\\types\\\\database\\\\MSampleWebfile\",
            \"webfile\": {
            \"firstname\": \"Sergey\",
                \"lastname\": \"Brin\",
                \"street\": \"Blumenstraße\",
                \"housenumber\": \"8\",
                \"postcode\": \"67433\",
                \"city\": \"Neustadt an der Weinstraße\",
                \"id\": \"2\",
                \"time\": \"4712\"
            }
        }
    ]";

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        $this->webfileStream = new MWebfileStream(null);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() : void
    {
    }

    /**
     * @throws \ReflectionException
     * @throws \webfilesframework\MWebfilesFrameworkException
     */
    public function testGetXML()
    {
        $webfileStream = new MWebfileStream(static::$webfileStreamAsXmlStringReference);
        $remarshalledXml = $webfileStream->getXML();

        $this->assertXmlStringEqualsXmlString(
            static::$webfileStreamAsXmlStringReference,
            $remarshalledXml
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \webfilesframework\MWebfilesFrameworkException
     */
    public function testGetJson()
    {
        $webfileStream = new MWebfileStream(static::$webfileStreamAsJsonStringReference);
        $remarshalledJson = $webfileStream->getJSON();

        echo $remarshalledJson;

        $this->assertJsonStringEqualsJsonString(
            static::$webfileStreamAsJsonStringReference,
            $remarshalledJson
        );
    }
    
    /**
     * @covers webfilesframework\core\datasystem\file\format\MWebfileStream::getArray
     */
    public function testGetWebfilesOnStringInput() {

        $webfileStream = new MWebfileStream(static::$webfileStreamAsXmlStringReference);
        $webfilesFromStream = $webfileStream->getArray();

        $this->assertEquals(2,count($webfilesFromStream));
    }

    public function testInstantiationWithMalformedStringThrowsException() {

    	self::expectException("\webfilesframework\MWebfilesFrameworkException");
    	self::expectExceptionMessage("Error: test that it not works");

        new MWebfileStream("Error: test that it not works");

    }

    public function testInstantiationWithWrongXmlThrowsException() {

	    self::expectException("\webfilesframework\MWebfilesFrameworkException");
	    self::expectExceptionMessage("No webfiles child exists on root element.");

        new MWebfileStream("<test>
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

        new MWebfileStream($webfiles);

    }

    
}
