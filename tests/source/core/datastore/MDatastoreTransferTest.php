<?php

namespace test\webfilesframework\core\datastore;

use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\MDatastoreException;
use webfilesframework\core\datastore\MDatastoreFactory;
use webfilesframework\core\datastore\MDatastoreTransfer;
use webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler;
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\file\system\MDirectory;
use webfilesframework\MWebfilesFrameworkException;


/**
 * @covers webfilesframework\core\datastore\MDatastoreTransfer
 */
class MDatastoreTransferTest extends MAbstractWebfilesFramworkTest {
    /**
     * @var MDatastoreTransfer
     */
    protected $object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {


        //new MDirectoryDatastore(new MDirectory("."));

        //$this->object = new MDatastoreTransfer($source, $target);
    }

    /**
     * @return MockObject
     */
    public function createDatabaseConnectionMock()
    {
        $databaseConnectionMock = $this
            ->createMock('webfilesframework\core\datasystem\database\MDatabaseConnection');
        return $databaseConnectionMock;
    }

    /**
     * @return MockObject
     */
    public function createPreparedDatabaseConnectionMock()
    {
        $databaseConnectionMock = $this->createDatabaseConnectionMock();
        $showTablesResultHandler = $this->createMockForShowTablesResultHandler();
        $webfilesResultHandler = $this->createMockForWebfilesResultHandler();

        // TODO möglichkeit finden hier ohne at() zu operieren, da refactorings sonst schnell tests brechnen können
        $databaseConnectionMock
            ->expects($this->at(2))
            ->method('queryAndHandle')
            ->with('SHOW TABLES FROM `webfiles`')
            ->willReturn($this->createMockForShowTablesResultHandler());

        $databaseConnectionMock
            ->expects($this->at(9))
            ->method('queryAndHandle')
            ->with('SELECT * FROM metadata')
            ->willReturn($this->createMockForMetadataResultHandler());

        $databaseConnectionMock
            ->expects($this->at(10))
            ->method('queryAndHandle')
            ->with('SELECT * FROM samplewebfile')
            ->willReturn($this->createMockForWebfilesResultHandler());

        // only in other transfer direction necessary:
        // check if webfiles exist
        /*$databaseConnectionMock
            ->expects($this->at(15))
            ->method('queryAndHandle')
            ->with('SELECT * FROM MSampleWebfile WHERE id=\'1\'')
            ->willReturn($this->createMockForWebfilesResultHandler());
        $databaseConnectionMock
            ->expects($this->at(34))
            ->method('queryAndHandle')
            ->with('SELECT * FROM MSampleWebfile WHERE id=\'2\'')
            ->willReturn($this->createMockForWebfilesResultHandler());*/


        $databaseConnectionMock->method('getDatabaseName')->willReturn('webfiles');
        return $databaseConnectionMock;
    }

	/**
	 * @return MockObject|MMysqlResultHandler
	 */
    public function createMockForShowTablesResultHandler()
    {
        $tablesMetaInformationResturnObject = (object)array(
            'Tables_in_webfiles' => 'MSampleWebfile',
        );

        $tablesMetaInformationResturnObject2 = (object)array(
            'Tables_in_webfiles' => 'metadata',
        );

        $showTablesResultHandler = $this
            ->createMock(
                'webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $showTablesResultHandler->method('getResultSize')->willReturn(1);

        //needed
        $showTablesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($tablesMetaInformationResturnObject, $tablesMetaInformationResturnObject2);

        return $showTablesResultHandler;
    }

    public function createMockForMetadataResultHandler()
    {
        $tablesMetaInformationResturnObject = (object)array(
            'classname' => 'webfilesframework\core\datastore\types\database\MSampleWebfile',
            'tablename' => 'samplewebfile'
        );

        $showTablesResultHandler = $this
            ->createMock(
                'webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $showTablesResultHandler->method('getResultSize')->willReturn(1);

        // needed
        $showTablesResultHandler
            ->expects($this->at(1))
            ->method('fetchNextResultObject')
            ->willReturn($tablesMetaInformationResturnObject);

        return $showTablesResultHandler;
    }

	/**
	 * @return MockObject
	 */
    public function createMockForWebfilesResultHandler()
    {

        $webfilesResultHandler = $this
            ->createMock(
                'webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $webfilesReturnObject = (object)array(
            'id' => '1',
            'firstname' => 'transfered',
            'lastname' => 'webfile',
            'street' => 'from',
            'city' => 'databaseDatastore',
            'housenumber' => '',
            'postcode' => '67433',
            'time' => 4711
        );

        $webfilesResultHandler->method('getResultSize')->willReturn(1);
        $webfilesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($webfilesReturnObject, null);

        return $webfilesResultHandler;
    }

	/**
	 * @return MDirectoryDatastore
	 * @throws MWebfilesFrameworkException
	 * @throws MDatastoreException
	 * @throws ReflectionException
	 */
    private function createDirectoryDatastore()
    {

        $directory = new MDirectory(
            __DIR__ . '/../../../resources/targetTransferDirectoryDatastore');

	    return new MDirectoryDatastore($directory);
    }

	/**
	 * @return MDirectoryDatastore
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    private function createSourceDirectoryDatastore()
    {
        $directory = new MDirectory(
            __DIR__ . '/../../../resources/folderDatastore');
	    return new MDirectoryDatastore($directory);
    }

	/**
	 * @return MDatabaseDatastore
	 */
    private function createDatabaseDatastore()
    {
        $connection = $this->createPreparedDatabaseConnectionMock();
	    return new MDatabaseDatastore($connection);
    }

	/**
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 * @covers webfilesframework\core\datastore\MDatastoreTransfer::transfer
	 */
    public function testTransferFromDatabaseToDirectory()
    {
        $source = $this->createDatabaseDatastore();
        $target = $this->createDirectoryDatastore();

        $target->deleteAll();

        $transfer = new MDatastoreTransfer(
            $source, $target
        );
        $transfer->transfer();

        self::assertEquals(
            1,
            count($target->getAllWebfiles()->getArray()));

    }

	/**
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public function testTransferFromDirectoryToDirectory()
    {
        $source = $this->createSourceDirectoryDatastore();
        $target = $this->createDirectoryDatastore();

        $target->deleteAll();

        $transfer = new MDatastoreTransfer(
            $source, $target
        );
        $transfer->transfer();

        self::assertEquals(
            3,
            count($target->getAllWebfiles()->getArray()));

        $target->normalize();

        /*self::assertEquals(
            count($target->getWebfilesAsArray()),
            count($source->getWebfilesAsArray()));*/

    }
}