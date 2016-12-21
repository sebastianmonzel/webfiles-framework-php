<?php


use simpleserv\webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection;
use simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile;

/**
 * Test class for MDatabaseDatastore.
 * Generated by PHPUnit on 2015-04-11 at 17:49:50.
 */
class MDirectoryDatastoreTest extends \PHPUnit_Framework_TestCase {
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

    /**
     * @return MSampleWebfile
     */
    public function createReferenceSampleObject1()
    {
        $reference = new MSampleWebfile();
        $reference->setId(1);
        $reference->setFirstname('Sebastian');
        $reference->setLastname('Monzel');
        $reference->setStreet('Blumenstraße');
        $reference->setHousenumber('4');
        $reference->setPostcode('67433');
        $reference->setCity('Neustadt an der Weinstraße');
        $reference->setTime('4711');
        return $reference;
    }

    public function createReferenceSampleObject2()
    {
        $reference = new MSampleWebfile();
        $reference->setId(2);
        $reference->setFirstname('Sergey');
        $reference->setLastname('Brin');
        $reference->setStreet('Blumenstraße');
        $reference->setHousenumber('8');
        $reference->setPostcode('67433');
        $reference->setCity('Neustadt an der Weinstraße');
        $reference->setTime('4712');
        return $reference;
    }

    public function createReferenceSampleObject3()
    {
        $reference = new MSampleWebfile();
        $reference->setId(3);
        $reference->setFirstname('Ludwig');
        $reference->setLastname('Hauber');
        $reference->setStreet('Blumenstraße');
        $reference->setHousenumber('8');
        $reference->setPostcode('67433');
        $reference->setCity('Neustadt an der Weinstraße');
        return $reference;
    }



    public function testSearchByTemplate() {

        $directoryDatastore = $this->createDirectoryDatastore();
        $template = new MSampleWebfile();
        $template->presetForTemplateSearch();
        $template->setFirstname('Sebastian');
        $result = $directoryDatastore->searchByTemplate($template);

        self::assertNotNull($result);
        self::assertTrue(is_array($result));
        self::assertEquals(1,count($result));

        $referenceObject = $this->createReferenceSampleObject1();

        self::assertEquals($referenceObject,array_values($result)[0]);

        $template = new MSampleWebfile();
        $template->presetForTemplateSearch();
        $template->setId('2');
        $result = $directoryDatastore->searchByTemplate($template);

        self::assertNotNull($result);
        self::assertTrue(is_array($result));
        self::assertEquals(1,count($result));

        $referenceObject = $this->createReferenceSampleObject2();

        self::assertEquals($referenceObject,array_values($result)[0]);

    }

    /**
     *
     */
    public function testCreateAndDeleteByTemplate() {

        $databaseDatastore = $this->createDirectoryDatastore();
        var_export($databaseDatastore->getWebfilesAsArray());
        self::assertEquals(2,count($databaseDatastore->getWebfilesAsArray()));
        $databaseDatastore->storeWebfile($this->createReferenceSampleObject3());
        self::assertEquals(3,count($databaseDatastore->getWebfilesAsArray()));

        $template = new MSampleWebfile();
        $template->presetForTemplateSearch();
        $template->setLastname("Hauber");
        $databaseDatastore->deleteByTemplate($template);
        self::assertEquals(2,count($databaseDatastore->getWebfilesAsArray()));
    }



    public function testGetWebfilesAsArray() {

        $directoryDatastore = $this->createDirectoryDatastore();
        $webfilesArray = $directoryDatastore->getWebfilesAsArray();


        self::assertTrue(is_array($webfilesArray));
        self::assertEquals(2,count($webfilesArray));
        $webfile1 = @array_shift(array_slice($webfilesArray,0,1));
        self::assertEquals($this->createReferenceSampleObject1(),$webfile1);
        $webfile2 = @array_shift(array_slice($webfilesArray,1,1));
        self::assertEquals($this->createReferenceSampleObject2(),$webfile2);

    }

}
