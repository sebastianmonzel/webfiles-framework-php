<?php

namespace test\webfilesframework\core\datastore\types\remote;

use ReflectionException;
use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datastore\types\remote\MRemoteDatastore;
use webfilesframework\MWebfilesFrameworkException;

class MRemoteDatastoreXmlTest extends MAbstractWebfilesFramworkTest {

	private const REMOTE_DATASTORE_URL = "http://webfiles.sebastianmonzel.de/jenkins/datastore/";

	protected $object;

	/**
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
	protected function setUp(): void
	{
		parent::setUp();
		
		$remoteDatastore = $this->createXmlRemoteDatastore();
		
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
		$remoteDatastore = $this->createXmlRemoteDatastore();
		
		// Lösche alle Webfiles nach dem Test
		$remoteDatastore->deleteByTemplate(new MSampleWebfile());
		
		parent::tearDown();
	}

	public function createXmlRemoteDatastore(): MRemoteDatastore {
		return new MRemoteDatastore(
			self::REMOTE_DATASTORE_URL,"xml"
		);
	}

	/**
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
	public function test_xml_getAllWebfiles() {

		$xmlRemoteDatastore = $this->createXmlRemoteDatastore();

        $this->doTestGetAllWebfiles($xmlRemoteDatastore);
	}

	/**
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
	public function test_xml_searchByTemplate_findsOneWebfile() {

		$remoteDatastore = $this->createXmlRemoteDatastore();

		$searchtemplate = new MSampleWebfile();
		$searchtemplate->presetForTemplateSearch();
		$searchtemplate->setLastname("Monzel");

		$webfilesArray = $remoteDatastore->searchByTemplate($searchtemplate)->getArray();

		self::assertNotNull($webfilesArray);
		self::assertTrue(is_array($webfilesArray));

		self::assertCount(1, $webfilesArray);

		/** @var MSampleWebfile $firstWebfile */
		$firstWebfile = $webfilesArray[0];

		self::assertEquals($firstWebfile->getFirstname(),"Sebastian");
		self::assertEquals($firstWebfile->getLastname(),"Monzel");
	}

	/**
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 */
	public function test_xml_searchByTemplate_findsNoWebfile() {

		$remoteDatastore = $this->createXmlRemoteDatastore();

		$searchtemplate = new MSampleWebfile();
		$searchtemplate->presetForTemplateSearch();
		$searchtemplate->setLastname("Schmidt");

		$webfilesArray = $remoteDatastore->searchByTemplate($searchtemplate)->getArray();

		self::assertNotNull($webfilesArray);
		self::assertTrue(is_array($webfilesArray));
		self::assertCount(0, $webfilesArray);
	}

	/**
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
	public function test_xml_storeWebfileAndDeleteItAgain() {

		$xmlRemoteDatastore = $this->createXmlRemoteDatastore();
		$this->doTestStoreAndDelete($xmlRemoteDatastore);
	}

	public function test_xml_isReadonly() {

		$remoteDatastore = $this->createXmlRemoteDatastore();

		$readOnly = $remoteDatastore->isReadOnly();
		self::assertFalse($readOnly);
	}

	public function test_xml_tryConnect() {

		$remoteDatastore = $this->createXmlRemoteDatastore();

		$tryConnect = $remoteDatastore->tryConnect();
		self::assertTrue($tryConnect);
	}

	public function test_xml_getLatestWebfiles() {

		$remoteDatastore = $this->createXmlRemoteDatastore();

		// TODO für die methode  muss man normalize auf dem directory datastore aufrufen -
		// TODO normalize gibt es jedoch nicht im remote datastore - soll ich die normalize durchleiten? - ich glaub hier sollt man pro speicherung für jedes neue webfile extra ne normalisierung vornehmen (schon angefangen?)
		//$latestWebfiles = $remoteDatastore->getLatestWebfiles(1);
		//self::assertCount(1, $latestWebfiles);
		self::assertEquals("","");
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
