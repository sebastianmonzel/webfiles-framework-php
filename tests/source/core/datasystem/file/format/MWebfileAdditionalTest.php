<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Additional tests for MWebfile to increase coverage
 * @covers webfilesframework\core\datasystem\file\format\MWebfile
 */
class MWebfileAdditionalTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::getId
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::setId
     */
    public function testGetAndSetId() {
        $webfile = new MWebfile();
        
        $webfile->setId(123);
        $this->assertEquals(123, $webfile->getId());
        
        $webfile->setId(456);
        $this->assertEquals(456, $webfile->getId());
    }

    /**
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::getTime
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::setTime
     */
    public function testGetAndSetTime() {
        $webfile = new MWebfile();
        
        $timestamp = time();
        $webfile->setTime($timestamp);
        $this->assertEquals($timestamp, $webfile->getTime());
    }

    /**
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::getTags
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::setTags
     */
    public function testGetAndSetTags() {
        $webfile = new MWebfile();
        
        $tags = array('php', 'testing', 'webfiles');
        $webfile->setTags($tags);
        $this->assertEquals($tags, $webfile->getTags());
    }

    /**
     * @covers webfilesframework\core\datasystem\file\format\MWebfile::__toString
     */
    public function testToString() {
        $webfile = new MWebfile();
        $webfile->setId(100);
        
        $string = (string)$webfile;
        $this->assertIsString($string);
    }
}
