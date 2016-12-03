<?php


use simpleserv\webfilesframework\core\datastore\MDatastoreTransfer;


/**
 * Test class for MDatastoreTransfer.
 */
class MDatastoreTransferTest extends PHPUnit_Framework_TestCase {
    /**
     * @var MDatastoreTransfer
     */
    protected $object;



    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {



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
            ->createMock('simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection');
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
            ->expects($this->at(3))
            ->method('queryAndHandle')
            ->with('SHOW TABLES FROM webfiles')
            ->willReturn($this->createMockForShowTablesResultHandler());
        $databaseConnectionMock
            ->expects($this->at(10))
            ->method('queryAndHandle')
            ->with('SHOW TABLES FROM webfiles')
            ->willReturn($this->createMockForShowTablesResultHandler());
        $databaseConnectionMock
            ->expects($this->at(22))
            ->method('queryAndHandle')
            ->with('SHOW TABLES FROM webfiles')
            ->willReturn($this->createMockForShowTablesResultHandler());
        $databaseConnectionMock
            ->expects($this->at(23))
            ->method('queryAndHandle')
            ->with('SHOW TABLES FROM webfiles')
            ->willReturn($this->createMockForShowTablesResultHandler());
        $databaseConnectionMock
            ->expects($this->at(29))
            ->method('queryAndHandle')
            ->with('SHOW TABLES FROM webfiles')
            ->willReturn($this->createMockForShowTablesResultHandler());
        $databaseConnectionMock
            ->expects($this->at(15))
            ->method('queryAndHandle')
            ->with('SELECT * FROM MSampleWebfile WHERE id=\'1\'')
            ->willReturn($this->createMockForWebfilesResultHandler());
        $databaseConnectionMock
            ->expects($this->at(34))
            ->method('queryAndHandle')
            ->with('SELECT * FROM MSampleWebfile WHERE id=\'2\'')
            ->willReturn($this->createMockForWebfilesResultHandler());
        $databaseConnectionMock
            ->expects($this->at(41))
            ->method('queryAndHandle')
            ->with('SHOW TABLES FROM webfiles')
            ->willReturn($this->createMockForShowTablesResultHandler());

        $databaseConnectionMock->method('getDatabaseName')->willReturn('webfiles');
        return $databaseConnectionMock;
    }

    /**
     * @return array
     */
    public function createMockForShowTablesResultHandler()
    {
        $tablesMetaInformationResturnObject = (object) [
            'Tables_in_webfiles' => 'MSampleWebfile',
        ];

        $showTablesResultHandler = $this
            ->createMock(
                'simpleserv\webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $showTablesResultHandler->method('getResultSize')->willReturn(1);


        $showTablesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($tablesMetaInformationResturnObject, null);
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
                'simpleserv\webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $webfilesReturnObject = (object) [
            'id' => '1',
            'firstname' => 'Peter',
            'lastname' => 'Schmidt',
            'street' => '',
            'housenumber' => '',
            'postcode' => '67433',
            'city' => 'Neustadt'
        ];

        $webfilesResultHandler->method('getResultSize')->willReturn(1);
        $webfilesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($webfilesReturnObject, null);

        return $webfilesResultHandler;
    }

    /**
     * @return null|\simpleserv\webfilesframework\core\datastore\MAbstractDatastore
     */
    private function createDirectoryDatastore() {

        $directory = new \simpleserv\webfilesframework\core\datasystem\file\system\MDirectory(
            __DIR__ . '/../../../resources/folderDatastore2');

        return \simpleserv\webfilesframework\core\datastore\MDatastoreFactory::createDatastore($directory);
    }

    private function createDatabaseDatastore() {
        $connection = $this->createPreparedDatabaseConnectionMock();
        return \simpleserv\webfilesframework\core\datastore\MDatastoreFactory::createDatastore($connection);
    }

    /**
     * @covers simpleserv\webfilesframework\core\datastore\MDatastoreTransfer::transfer
     */
    public function testTransfer() {

        $source = $this->createDatabaseDatastore();
        $target = $this->createDirectoryDatastore();

        $transfer = new MDatastoreTransfer(
            $target,$source
        );

        $transfer->transfer();


        self::assertEquals(
            2,
            count($source->getWebfilesAsArray()));

        self::assertEquals(
            count($target->getWebfilesAsArray()),
            count($source->getWebfilesAsArray()));

    }
}
