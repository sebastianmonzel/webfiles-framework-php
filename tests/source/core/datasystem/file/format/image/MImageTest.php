<?php


use simpleserv\webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection;
use simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile;

/**
 * @covers simpleserv\webfilesframework\core\datasystem\file\format\image\MImage
 */
class MImageTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var MDatabaseDatasourceDatastore
     */
    protected $object;

    /**
     * @return \simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatastore
     */
    public function createDirectoryDatastore()
    {
        $directoryDatastore = new \simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatastore(
            new \simpleserv\webfilesframework\core\datasystem\file\system\MDirectory(__DIR__ . '/../../../../../resources/folderDatastore'));
        return $directoryDatastore;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $connection = new MDatabaseConnection();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }

    public function testReadExifDate() {

        $image = new \simpleserv\webfilesframework\core\datasystem\file\format\image\MImage(
            __DIR__ . '/../../../../../../resources/folderDatastore/5302654.jpg');

        $exifDate = $image->readExifDate();
        $this->assertEquals(1482418147,$exifDate);
    }

}
