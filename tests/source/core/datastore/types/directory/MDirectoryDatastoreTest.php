<?php


use simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile;
use simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;

/**
 * @covers simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatastore
 */
class MDirectoryDatastoreTest extends \PHPUnit_Framework_TestCase {

    protected $object;

    /**
     * @return \simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatastore
     */
    public function createDirectoryDatastore()
    {
        $directoryDatastore = new MDirectoryDatastore(
            new MDirectory(__DIR__ . '/../../../../../resources/folderDatastore'));
        return $directoryDatastore;
    }

    /**
     * @return \simpleserv\webfilesframework\core\datastore\MAbstractDatastore
     */
    private function createTempDirectoryDatastore()
    {

        $directory = new MDirectory(
            __DIR__ . '/../../../../../resources/targetTransferDirectoryDatastore');

        return \simpleserv\webfilesframework\core\datastore\MDatastoreFactory::createDatastore($directory);
    }


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
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
        $reference->setStreet('Blumenstrasse');
        $reference->setHousenumber('4');
        $reference->setPostcode('67433');
        $reference->setCity('Neustadt an der Weinstrasse');
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
        self::assertEquals(3,count($databaseDatastore->getWebfilesAsArray()));
        $databaseDatastore->storeWebfile($this->createReferenceSampleObject3());
        self::assertEquals(4,count($databaseDatastore->getWebfilesAsArray()));

        $template = new MSampleWebfile();
        $template->presetForTemplateSearch();
        $template->setLastname("Hauber");
        $databaseDatastore->deleteByTemplate($template);
        self::assertEquals(3,count($databaseDatastore->getWebfilesAsArray()));
    }



    public function testGetWebfilesAsArray() {

        $directoryDatastore = $this->createDirectoryDatastore();
        $webfilesArray = $directoryDatastore->getWebfilesAsArray();


        self::assertTrue(is_array($webfilesArray));
        self::assertEquals(3,count($webfilesArray));
        $webfile1 = @array_shift(array_slice($webfilesArray,0,1));
        //self::assertEquals($this->createReferenceSampleObject1(),$webfile1);

	    $webfile2 = @array_shift(array_slice($webfilesArray,1,1));
	    self::assertEquals($this->createReferenceSampleObject1(),$webfile2);
        $webfile3 = @array_shift(array_slice($webfilesArray,2,1));
        self::assertEquals($this->createReferenceSampleObject2(),$webfile3);

    }

    public function testDoNormalizeFileOnlyOnce() {

        $directoryDatastore = $this->createDirectoryDatastore();
        $tmpDatastore = $this->createTempDirectoryDatastore();
        $tmpDatastore->deleteAll();

        $transfer = new \simpleserv\webfilesframework\core\datastore\MDatastoreTransfer(
            $directoryDatastore,$tmpDatastore);

        $transfer->transfer();

        $tmpDatastore->normalize();
        $filenamesAfterFirstNormalisation = $tmpDatastore->getDirectory()->getFileNames();

        $tmpDatastore->normalize();
        $filenamesAfterSecondNormalisation = $tmpDatastore->getDirectory()->getFileNames();

        self::assertEquals($filenamesAfterFirstNormalisation[3],$filenamesAfterSecondNormalisation[3]);

    }


    public function testNormalizeLocalDirectory() {

        /*$directoryDatastore = $this->createDirectoryDatastore();

        $directoryDatastore = new \simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatastore(
            new \simpleserv\webfilesframework\core\datasystem\file\system\MDirectory('E:\owncloud\familie\bilder\2016_12_24__weihnachten-bei-drochterts-test'));

        $webfilesArray = $directoryDatastore->getWebfilesAsArray();

        self::assertTrue(is_array($webfilesArray));
        self::assertEquals(11,count($webfilesArray));
*/
        //$directoryDatastore->normalize(false,true);

    }

}
