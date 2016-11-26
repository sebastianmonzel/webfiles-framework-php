<?php


use simpleserv\webfilesframework\core\datastore\MDatastoreFactory;
use simpleserv\webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use simpleserv\webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
use simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile;

/**
 * Test class for MDatabaseDatastore.
 * Generated by PHPUnit on 2015-04-11 at 17:49:50.
 */
class MDatabaseDatastoreTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var MDatabaseDatasourceDatastore
     */
    protected $object;

    /**
     * @param $tablesMetaInformationResturnObject
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createWebfilesResultHandlerMock()
    {

        $webfilesResultHandler = $this
            ->createMock(
                'simpleserv\webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');




        $webfilesReturnObject = new class
        {
        };

        $webfilesReturnObject->id = '1';
        $webfilesReturnObject->firstname = 'Peter';
        $webfilesReturnObject->lastname = 'Schmidt';
        $webfilesReturnObject->street = '';
        $webfilesReturnObject->housenumber = '';
        $webfilesReturnObject->postcode = '67433';
        $webfilesReturnObject->city = 'Neustadt';


        $webfilesResultHandler->method('getResultSize')->willReturn(1);
        $webfilesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($webfilesReturnObject, null);

        return $webfilesResultHandler;
    }

    /**
     * @return array
     */
    public function createShowTablesResultHandlerMock()
    {
        // TODO umbauen wenn unten aufgeführte erzeugung von klasse in alten php versionen probleme macht

        /*$object = (object) [
            'propertyOne' => 'foo',
            'propertyTwo' => 42,
        ];*/

        $tablesMetaInformationResturnObject = new class
        {
        };
        $tablesMetaInformationResturnObject->Tables_in_webfiles = 'MSampleWebfile';

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

    /**
     * 
     */
    public function testGetByTemplate() {


        $databaseConnectionMock = $this
            ->createMock('simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection');

        $showTablesResultHandler = $this->createShowTablesResultHandlerMock();
        $databaseConnectionMock->expects($this->at(2))->method('queryAndHandle')->with('SHOW TABLES FROM webfiles')->willReturn($showTablesResultHandler);

        $webfilesResultHandler = $this->createWebfilesResultHandlerMock();
        $databaseConnectionMock->expects($this->at(7))->method('queryAndHandle')->willReturn($webfilesResultHandler);

        $databaseConnectionMock->method('getDatabaseName')->willReturn('webfiles');

        $databaseDatastore = new MDatabaseDatastore($databaseConnectionMock);
        $template = new MSampleWebfile();
        $template->presetForTemplateSearch();

        $result = $databaseDatastore->searchByTemplate($template);

        self::assertNotNull($result);
        self::assertTrue(is_array($result));
        self::assertEquals(1,count($result));

        $reference = new MSampleWebfile();
        $reference->setId(1);
        $reference->setFirstname('Peter');
        $reference->setLastname('Schmidt');
        $reference->setStreet('');
        $reference->setStreet('');
        $reference->setPostcode('67433');
        $reference->setCity('Neustadt');

        self::assertEquals($reference,$result[0]);
        //->method('query');
        //->with($this->stringContains('CREATE TABLE'));
            //->with($this->greaterThan(0), $this->stringContains('Something'));
    }


    public function testCreationOfNewDatabaseInCaseNoTableExists2() {
        // TODO erweitern
        $showTablesResultHandler = $this
            ->createMock(
                'simpleserv\webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $showTablesResultHandler->method('getResultSize')->willReturn(1);

        // TODO umbauen wenn unten aufgeführte erzeugung von klasse in alten php versionen probleme macht

        /*$object = (object) [
            'propertyOne' => 'foo',
            'propertyTwo' => 42,
        ];*/

        $tablesMetaInformationResturnObject = new class{};
        //$tablesMetaInformationResturnObject->Tables_in_webfiles = 'MSampleWebfile';
        $tablesMetaInformationResturnObject->Tables_in_webfiles = 'test';




        $showTablesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($tablesMetaInformationResturnObject,null);

        $databaseConnectionMock = $this
            ->createMock('simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection');
        $databaseConnectionMock->method('queryAndHandle')->with('SHOW TABLES FROM webfiles')->willReturn($showTablesResultHandler);
        $databaseConnectionMock->method('getDatabaseName')->willReturn('webfiles');

        $databaseDatastore = new MDatabaseDatastore($databaseConnectionMock);
        $template = new MSampleWebfile();
        $template->presetForTemplateSearch();

        $databaseConnectionMock->expects($this->exactly(3))
            ->method('queryAndHandle')->with('SHOW TABLES FROM webfiles');
        $result = $databaseDatastore->searchByTemplate($template);

        self::assertNotNull($result);
        self::assertTrue(is_array($result));
       // self::assertEquals(1,count($result));


        $reference = new MSampleWebfile();
        $reference->setId(1);
        $reference->setFirstname('Peter');
        $reference->setLastname('Schmidt');
        $reference->setStreet('');
        $reference->setStreet('');
        $reference->setPostcode('67433');
        $reference->setCity('Neustadt');

        //self::assertEquals($reference,$result[0]);
        //->method('query');
        //->with($this->stringContains('CREATE TABLE'));
        //->with($this->greaterThan(0), $this->stringContains('Something'));
    }


    public function testCreationOfNewDatabaseInCaseNoTableExists() {

        // TODO erweitern
        $template = new MSampleWebfile();

        $stub = $this->createMock('simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection');
        $stub->method('getDatabaseName')->willReturn('webfiles');

        $resultHandler = $this->createMock('simpleserv\webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');
        $resultHandler->method('getResultSize')->willReturn(0);

        $stub->method('queryAndHandle')->willReturn($resultHandler);

        $databaseDatastore = new MDatabaseDatastore($stub);

        $databaseDatastore->searchByTemplate($template);

    }
}
