<?php

use webfilesframework\core\datastore\MDatastoreFactory;
use webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * @covers webfilesframework\core\datasystem\file\format\MWebfile
 *
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
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::getSimplifiedAttributeName
     */
    public function testGetSimplifiedAttributeName() {
    	
    	$calculatedSimplifiedAttributename = MWebfile::getSimplifiedAttributeName("m_sName");
    	$referencedSimplifiedAttributename = "name";
    	$this->assertEquals($referencedSimplifiedAttributename,$calculatedSimplifiedAttributename);
    }

    public function testUnmarshallingAndMarshallingWebfile() {

        // UNMARSHALL
        $file = new \webfilesframework\core\datasystem\file\system\MFile(
            __DIR__ . '/../../../../../resources/folderDatastore/sampleWebfile1.webfile');
        $sample = MWebfile::staticUnmarshall($file->getContent());

        // MARSHALL
        $marshalledWebfile = $sample->marshall(true);

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../../../resources/folderDatastore/sampleWebfile1.webfile',
            $marshalledWebfile
            );
    }

    public function checkConventionsOnEveryGivenWebfileInClasspath() {
        // TODO probably https://github.com/hanneskod/classtools fits for
        // searching classes in classpath
    }

}
