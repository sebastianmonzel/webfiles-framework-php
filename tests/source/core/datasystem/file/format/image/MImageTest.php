<?php


use PHPUnit\Framework\TestCase;
use webfilesframework\core\datastore\MDatastoreException;
use webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\database\MDatabaseConnection;
use webfilesframework\core\datasystem\file\format\media\image\MImage;
use webfilesframework\core\datasystem\file\system\MDirectory;
use webfilesframework\MWebfilesFrameworkException;

/**
 * @covers webfilesframework\core\datasystem\file\format\media\image\MImage
 */
class MImageTest extends TestCase {
    /**
     * @var MDatabaseDatastore
     */
    protected $object;

	/**
	 * @return MDirectoryDatastore
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 * @throws MDatastoreException
	 */
    public function createDirectoryDatastore()
    {
        $directoryDatastore = new MDirectoryDatastore(
            new MDirectory(__DIR__ . '/../../../../../resources/folderDatastore')
        );
        return $directoryDatastore;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void {
        $connection = new MDatabaseConnection();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() : void {
    }

    public function testReadExifDate() {

        $image = new MImage(
            __DIR__ . '/../../../../../../resources/folderDatastore/5302654.jpg');

        $exifDate = $image->readExifDate();
        $this->assertEquals(1482418147,$exifDate);
    }
}
