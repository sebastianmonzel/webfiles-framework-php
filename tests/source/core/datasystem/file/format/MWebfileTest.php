<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\MDatastoreFactory;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\system\MFile;

/**
 * @covers webfilesframework\core\datasystem\file\format\MWebfile
 *
 * Test class for MWebfileTest.
 */
class MWebfileTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::getSimplifiedAttributeName
     */
    public function testGetSimplifiedAttributeName() {
    	
    	$calculatedSimplifiedAttributename = MWebfile::getSimplifiedAttributeName("m_sName");
    	$referencedSimplifiedAttributename = "name";
    	$this->assertEquals($referencedSimplifiedAttributename,$calculatedSimplifiedAttributename);
    }

    /**
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::getSimplifiedAttributeName
     */
    public function testIsNumericAttribute() {

        $isNumericAttributeGiven = MWebfile::isNumericAttribute("m_iId");
        $this->assertEquals(true,$isNumericAttributeGiven);

        $isNumericAttributeNotGiven = MWebfile::isNumericAttribute("m_bIsRed");
        $this->assertEquals(false,$isNumericAttributeNotGiven);
    }


    public function testUnmarshallingAndMarshallingXmlWebfile() {

        // UNMARSHALL
        $file = new MFile(
            __DIR__ . '/../../../../../resources/folderDatastore/sampleWebfile1.webfile');
        $sample = MWebfile::staticUnmarshall($file->getContent());

        // MARSHALL
        $marshalledWebfile = $sample->marshall(true);

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/../../../../../resources/folderDatastore/sampleWebfile1.webfile',
            $marshalledWebfile
            );
    }

    public function testUnmarshallingAndMarshallingJsonWebfile() {

        // UNMARSHALL
        $file = new MFile(
            __DIR__ . '/../../../../../resources/folderDatastoreJson/sampleWebfile1.webfile');
        $sample = MWebfile::staticUnmarshall($file->getContent());

        // MARSHALL
        $marshalledWebfile = $sample->marshall(true, true);

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/../../../../../resources/folderDatastoreJson/sampleWebfile1.webfile',
            $marshalledWebfile
        );
    }



    public function checkConventionsOnEveryGivenWebfileInClasspath() {
        // TODO probably https://github.com/hanneskod/classtools fits for
        // searching classes in classpath
    }

}
