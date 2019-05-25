<?php


use webfilesframework\core\datasystem\database\MDatabaseConnection;
use webfilesframework\core\datasystem\file\format\media\image\MImage;

/**
 * @covers webfilesframework\core\datasystem\file\format\media\MImage
 */
class MImageTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var MDatabaseDatasourceDatastore
     */
    protected $object;

	/**
	 * @return \webfilesframework\core\datastore\types\directory\MDirectoryDatastore
	 * @throws ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 */
    public function createDirectoryDatastore()
    {
        $directoryDatastore = new \webfilesframework\core\datastore\types\directory\MDirectoryDatastore(
            new \webfilesframework\core\datasystem\file\system\MDirectory(__DIR__ . '/../../../../../resources/folderDatastore'));
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

        $image = new MImage(
            __DIR__ . '/../../../../../../resources/folderDatastore/5302654.jpg');

        $exifDate = $image->readExifDate();
        $this->assertEquals(1482418147,$exifDate);
    }

}
