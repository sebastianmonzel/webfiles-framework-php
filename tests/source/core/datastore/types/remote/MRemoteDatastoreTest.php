<?php

namespace test\webfilesframework\core\datastore\types;

use ReflectionException;
use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datastore\types\remote\MRemoteDatastore;
use webfilesframework\MWebfilesFrameworkException;

class MRemoteDatastoreTest extends MAbstractWebfilesFramworkTest {

	protected $object;

	public function createXmlRemoteDatastore() {
		$remoteDatastore = new MRemoteDatastore(
			"http://webfiles.sebastianmonzel.de/jenkins/datastore/?xml"
		);

		return $remoteDatastore;
	}

    public function createJsonRemoteDatastore() {
        $remoteDatastore = new MRemoteDatastore(
            "http://webfiles.sebastianmonzel.de/jenkins/datastore/?json"
        );

        return $remoteDatastore;
    }

	/**
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
	public function test_xml_getAllWebfiles() {

		$xmlRemoteDatastore = $this->createXmlRemoteDatastore();

		$webfilesAsStream = $xmlRemoteDatastore->getAllWebfiles();

		self::assertNotNull($webfilesAsStream);
		$webfilesArray = $webfilesAsStream->getArray();
		self::assertTrue(is_array( $webfilesArray ));
		self::assertCount(2, $webfilesArray);

		/** @var MSampleWebfile $firstWebfile */
		$firstWebfile = $webfilesArray[0];

		self::assertEquals("Sebastian", $firstWebfile->getFirstname());
		self::assertEquals("Monzel", $firstWebfile->getLastname());

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
		$this->testStoreAndDelete($xmlRemoteDatastore);
	}

   public function test_json_storeWebfileAndDeleteItAgain() {

        $jsonRemoteDatastore = $this->createJsonRemoteDatastore();
        $this->testStoreAndDelete($jsonRemoteDatastore);
   }

    /**
     * @param MRemoteDatastore $remoteDatastore
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    private function testStoreAndDelete(MRemoteDatastore $remoteDatastore): void
    {
        $webfileToStore = new MSampleWebfile();
        $webfileToStore->setLastname("Schmidt");

        $webfileToStore->setId(4);
        $webfilesStream = $remoteDatastore->storeWebfile($webfileToStore);
        self::assertCount(3, $webfilesStream->getArray());

        $webfileToStore->setId(5);
        $webfilesStream = $remoteDatastore->storeWebfile($webfileToStore);
        self::assertCount(4, $webfilesStream->getArray());

        $searchtemplate = new MSampleWebfile();
        $searchtemplate->presetForTemplateSearch();
        $searchtemplate->setLastname("Schmidt");
        $webfilesStream = $remoteDatastore->deleteByTemplate($searchtemplate);
        self::assertCount(2, $webfilesStream->getArray());
    }
    /*
	public function testGetNextWebfileForTimestamp() {

		$remoteDatastore = $this->createRemoteDatastore();

		$next_webfile_for_timestamp = $remoteDatastore->getNextWebfileForTimestamp( -1 );
		// TODO für die methode  muss man normalize auf dem directory datastore aufrufen -
		// TODO normalize gibt es jedoch nicht im remote datastore - soll ich die normalize durchleiten?
		//self::assertNotNull($next_webfile_for_timestamp);
		self::assertEquals("","");
	}*/

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



}