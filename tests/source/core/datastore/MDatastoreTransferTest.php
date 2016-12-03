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
            ->expects($this->at(2))
            ->method('queryAndHandle')
            ->with('SHOW TABLES FROM webfiles')
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
        $tablesMetaInformationResturnObject = (object) [
            'Tables_in_webfiles' => 'MSampleWebfile',
        ];

        $tablesMetaInformationResturnObject2 = (object) [
            'Tables_in_webfiles' => 'metadata',
        ];

        $showTablesResultHandler = $this
            ->createMock(
                'simpleserv\webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $showTablesResultHandler->method('getResultSize')->willReturn(1);

        //needed
        $showTablesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($tablesMetaInformationResturnObject,$tablesMetaInformationResturnObject2);

        return $showTablesResultHandler;
    }

    public function createMockForMetadataResultHandler()
    {
        $tablesMetaInformationResturnObject = (object) [
            'classname' => 'simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile',
            'tablename' => 'samplewebfile'
        ];

        $showTablesResultHandler = $this
            ->createMock(
                'simpleserv\webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

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
                'simpleserv\webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $webfilesReturnObject = (object) [
            'id' => '1',
            'firstname' => 'transfered',
            'lastname' => 'webfile',
            'street' => 'from',
            'city' => 'databaseDatastore',
            'housenumber' => '',
            'postcode' => '67433'
        ];

        $webfilesResultHandler->method('getResultSize')->willReturn(1);
        $webfilesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($webfilesReturnObject,null);

        return $webfilesResultHandler;
    }

    /**
     * @return null|\simpleserv\webfilesframework\core\datastore\MAbstractDatastore
     */
    private function createDirectoryDatastore() {

        $directory = new \simpleserv\webfilesframework\core\datasystem\file\system\MDirectory(
            __DIR__ . '/../../../resources/folderDatastoreToTransferDataFromDatabase');

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

        echo "test1";
        $source = $this->createDatabaseDatastore();
        $target = $this->createDirectoryDatastore();

        $template = new \simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile();
        $template->presetForTemplateSearch();
        $target->deleteByTemplate($template);

        $transfer = new MDatastoreTransfer(
            $source,$target
        );
        $transfer->transfer();
        echo "test2";

        self::assertEquals(
            1,
            count($target->getWebfilesAsArray()));

        /*self::assertEquals(
            count($target->getWebfilesAsArray()),
            count($source->getWebfilesAsArray()));*/
    }
}
