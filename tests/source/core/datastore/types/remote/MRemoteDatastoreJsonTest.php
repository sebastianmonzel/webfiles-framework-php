<?php

namespace test\webfilesframework\core\datastore\types\remote;

use ReflectionException;
use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datastore\types\remote\MRemoteDatastore;
use webfilesframework\MWebfilesFrameworkException;

class MRemoteDatastoreJsonTest extends MAbstractWebfilesFramworkTest {

	private const REMOTE_DATASTORE_URL = "http://webfiles.sebastianmonzel.de/jenkins/datastore/";

	protected $object;

    /**
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $remoteDatastore = $this->createJsonRemoteDatastore();
        
        // Initialisiere Webfile mit Sebastian Monzel
        $webfile = new MSampleWebfile();
        $webfile->setId(1);
        $webfile->setFirstname("Sebastian");
        $webfile->setLastname("Monzel");
        
        $remoteDatastore->storeWebfile($webfile);
    }

    /**
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    protected function tearDown(): void
    {
        $remoteDatastore = $this->createJsonRemoteDatastore();
        
        // Lösche alle Webfiles nach dem Test
		$remoteDatastore->deleteByTemplate(new MSampleWebfile());
        
        parent::tearDown();
    }

    public function createJsonRemoteDatastore(): MRemoteDatastore {
        return new MRemoteDatastore(
            self::REMOTE_DATASTORE_URL,"json"
        );
    }

    /**
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    public function test_json_getAllWebfiles() {

        $jsonRemoteDatastore = $this->createJsonRemoteDatastore();

        $this->doTestGetAllWebfiles($jsonRemoteDatastore);
    }

    /**
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    public function test_json_storeWebfileAndDeleteItAgain() {

        $jsonRemoteDatastore = $this->createJsonRemoteDatastore();
        $this->doTestStoreAndDelete($jsonRemoteDatastore);
    }

    /**
     * @param MRemoteDatastore $remoteDatastore
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    private function doTestStoreAndDelete(MRemoteDatastore $remoteDatastore): void
    {
        $webfileToStore = new MSampleWebfile();
		$webfileToStore->setLastname("Schmidt");

        $webfileToStore->setId(4);
        $webfilesStream = $remoteDatastore->storeWebfile($webfileToStore);
        self::assertCount(2, $webfilesStream->getArray());

        $webfileToStore->setId(5);
        $webfilesStream = $remoteDatastore->storeWebfile($webfileToStore);
        self::assertCount(3, $webfilesStream->getArray());

        $searchtemplate = new MSampleWebfile();
        $searchtemplate->presetForTemplateSearch();
		$searchtemplate->setLastname("Schmidt");
        $webfilesStream = $remoteDatastore->deleteByTemplate($searchtemplate);

        self::assertGreaterThanOrEqual(2, $webfilesStream->getArray());
    }

    /**
     * @param MRemoteDatastore $remoteDatastore
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    private function doTestGetAllWebfiles(MRemoteDatastore $remoteDatastore): void
    {
        $webfilesAsStream = $remoteDatastore->getAllWebfiles();

        self::assertNotNull($webfilesAsStream);
        $webfilesArray = $webfilesAsStream->getArray();
        self::assertTrue(is_array($webfilesArray));
        self::assertCount(1, $webfilesArray);

        /** @var MSampleWebfile $firstWebfile */
        $firstWebfile = $webfilesArray[0];

        self::assertEquals("Sebastian", $firstWebfile->getFirstname());
        self::assertEquals("Monzel", $firstWebfile->getLastname());
    }
}
