<?php


use PHPUnit\Framework\TestCase;
use webfilesframework\core\datastore\MDatastoreTransfer;


/**
 * @covers webfilesframework\core\datastore\MDatastoreTransfer
 */
class MDatastoreTransferTest extends TestCase
{
    /**
     * @var MDatastoreTransfer
     */
    protected $object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {


        //new MDirectoryDatastore(new MDirectory("."));

        //$this->object = new MDatastoreTransfer($source, $target);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createDatabaseConnectionMock()
    {
        $databaseConnectionMock = $this
            ->createMock('webfilesframework\core\datasystem\database\MDatabaseConnection');
        return $databaseConnectionMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
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
     * @return array
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
     * @param $tablesMetaInformationResturnObject
     * @return PHPUnit_Framework_MockObject_MockObject
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
     * @return \webfilesframework\core\datastore\types\directory\MDirectoryDatastore
     */
    private function createDirectoryDatastore()
    {

        $directory = new \webfilesframework\core\datasystem\file\system\MDirectory(
            __DIR__ . '/../../../resources/targetTransferDirectoryDatastore');

        return \webfilesframework\core\datastore\MDatastoreFactory::createDatastore($directory);
    }

    private function createSourceDirectoryDatastore()
    {

        $directory = new \webfilesframework\core\datasystem\file\system\MDirectory(
            __DIR__ . '/../../../resources/folderDatastore');

        return \webfilesframework\core\datastore\MDatastoreFactory::createDatastore($directory);
    }


    private function createDatabaseDatastore()
    {
        $connection = $this->createPreparedDatabaseConnectionMock();
        return \webfilesframework\core\datastore\MDatastoreFactory::createDatastore($connection);
    }

    /**
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
            count($target->getWebfilesAsArray()));

    }

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
            count($target->getWebfilesAsArray()));

        $target->normalize();

        /*self::assertEquals(
            count($target->getWebfilesAsArray()),
            count($source->getWebfilesAsArray()));*/

    }
}