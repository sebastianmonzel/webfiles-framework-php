<?php


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use webfilesframework\core\datastore\MDatastoreException;
use webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler;
use webfilesframework\core\datasystem\database\MDatabaseConnection;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\MWebfilesFrameworkException;

/**
 * @covers webfilesframework\core\datastore\types\database\MDatabaseDatastore
 * @covers webfilesframework\core\datasystem\database\MDatabaseConnection
 */
class MDatabaseDatastoreTest extends TestCase {
    /**
     * @var MDatabaseDatasourceDatastore
     */
    protected $object;



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

    /**
     * @return MSampleWebfile
     */
    public function createReferenceSampleObject()
    {
        $reference = new MSampleWebfile();
        $reference->setId(1);
        $reference->setFirstname('Peter');
        $reference->setLastname('Schmidt');
        $reference->setStreet('');
        $reference->setStreet('');
        $reference->setPostcode('67433');
        $reference->setCity('Neustadt');
        $reference->setTime(4711);
        return $reference;
    }

	/**
	 * @return MockObject|MDatabaseConnection
	 */
	public function createDatabaseConnectionMock()
    {
        $databaseConnectionMock = $this
            ->createMock('webfilesframework\core\datasystem\database\MDatabaseConnection');
        return $databaseConnectionMock;
    }

	/**
	 * @return MockObject|MDatabaseConnection
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
            ->willReturn($showTablesResultHandler);
        $databaseConnectionMock
            ->expects($this->at(7))
            ->method('queryAndHandle')
            ->with('SELECT * FROM MSampleWebfile')
            ->willReturn($webfilesResultHandler);

        $databaseConnectionMock->method('getDatabaseName')->willReturn('webfiles');
        return $databaseConnectionMock;
    }

	/**
	 * @return MockObject|MMysqlResultHandler
	 */
    public function createMockForShowTablesResultHandler()
    {
        $tablesMetaInformationResturnObject = (object) array(
            'Tables_in_webfiles' => 'MSampleWebfile',
        );

        $showTablesResultHandler = $this
            ->createMock(
                'webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $showTablesResultHandler->method('getResultSize')->willReturn(1);


        $showTablesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($tablesMetaInformationResturnObject, null);
        return $showTablesResultHandler;
    }

	/**
	 * @return MockObject|MMysqlResultHandler
	 */
    public function createMockForWebfilesResultHandler()
    {

        $webfilesResultHandler = $this
            ->createMock(
                'webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');

        $webfilesReturnObject = (object) array(
            'id' => '1',
            'firstname' => 'Peter',
            'lastname' => 'Schmidt',
            'street' => '',
            'housenumber' => '',
            'postcode' => '67433',
            'city' => 'Neustadt',
            'time' => 4711
        );

        $webfilesResultHandler->method('getResultSize')->willReturn(1);
        $webfilesResultHandler
            ->method('fetchNextResultObject')
            ->willReturn($webfilesReturnObject, null);

        return $webfilesResultHandler;
    }

	/**
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 * @throws MDatastoreException
	 */
    public function testSearchByTemplate() {

        $databaseConnectionMock = $this->createPreparedDatabaseConnectionMock();

        $databaseDatastore = new MDatabaseDatastore($databaseConnectionMock);
        $template = new MSampleWebfile();
        $template->presetForTemplateSearch();

        $result = $databaseDatastore->searchByTemplate($template);

        self::assertNotNull($result);
        self::assertTrue(is_array($result));
        self::assertEquals(1,count($result));

        $referenceObject = $this->createReferenceSampleObject();

        // TODO im MDirectoryDatastore wird der timestamp als array index gesetzt - hier ist das anders
        self::assertEquals($referenceObject,array_values($result)[0]);
    }

	/**
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public function testDeleteByTemplate() {

        $databaseConnectionMock = $this->createDatabaseConnectionMock();

        $showTablesResultHandler = $this->createMockForShowTablesResultHandler();
        $databaseConnectionMock->expects($this->once())->method('queryAndHandle')->with('SHOW TABLES FROM `webfiles`')->willReturn($showTablesResultHandler);

        $webfilesResultHandler = $this->createMockForWebfilesResultHandler();
        $databaseConnectionMock->expects(self::at(8))->method('query')->with('DELETE FROM metadatanormalization where webfileid in (SELECT id FROM MSampleWebfile WHERE ) AND classname = \'webfilesframework\\\\core\\\\datastore\\\\types\\\\database\\\\MSampleWebfile\'')->willReturn($webfilesResultHandler);
        $databaseConnectionMock->expects(self::at(9))->method('query')->with('DELETE FROM MSampleWebfile')->willReturn($webfilesResultHandler);

        $databaseConnectionMock->method('getDatabaseName')->willReturn('webfiles');

        $databaseDatastore = new MDatabaseDatastore($databaseConnectionMock);
        $template = new MSampleWebfile();
        $template->presetForTemplateSearch();

        $result = $databaseDatastore->deleteByTemplate($template);

        self::assertNull($result);
    }

	/**
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public function testCreationOfNewTableIfItDoesNotExists() {

        // TODO erweitern
        $template = new MSampleWebfile();

        $stub = $this->createMock(
            'webfilesframework\core\datasystem\database\MDatabaseConnection');
        $stub->method('getDatabaseName')->willReturn('webfiles');

        $resultHandler = $this->createMock(
            'webfilesframework\core\datastore\types\database\resultHandler\MMysqlResultHandler');
        $resultHandler->method('getResultSize')->willReturn(0);

        // TODO warum wird die query so oft ausgeführt? prüfung metadaten + webfiletabelle
        $stub->expects(self::exactly(3))
            ->method('queryAndHandle')
            ->with('SHOW TABLES FROM `webfiles`')
            ->willReturn($resultHandler);

        $databaseDatastore = new MDatabaseDatastore($stub);
        $stub->expects(self::at(13))->method('query')
            ->with('CREATE TABLE IF NOT EXISTS `metadata` (`id` int(10) NOT NULL AUTO_INCREMENT,`classname` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`version` int(50) NOT NULL,`tablename` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;')
            ->willReturn(null);

        $stub->expects(self::at(15))->method('query')
            ->with('INSERT INTO metadata(classname, version, tablename) VALUES (\'webfilesframework\\\\core\\\\datastore\\\\types\\\\database\\\\MSampleWebfile\' , \'1\' , \'MSampleWebfile\');')
            ->willReturn(null);

        $stub->expects(self::at(16))->method('query')
            ->with('CREATE TABLE IF NOT EXISTS `MSampleWebfile` (`id` int(10) NOT NULL AUTO_INCREMENT,`firstname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`lastname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`street` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`housenumber` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`postcode` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`city` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,`time` int(24) NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;')
            ->willReturn(null);

        $result = $databaseDatastore->searchByTemplate($template);

        self::assertNotNull($result);
        self::assertTrue(is_array($result));
        self::assertEquals(0,count($result));
    }
}
