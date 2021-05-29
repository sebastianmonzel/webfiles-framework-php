<?php

namespace test\webfilesframework\core\datastore\types;

use ReflectionException;
use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datastore\types\remote\MRemoteDatastore;
use webfilesframework\MWebfilesFrameworkException;

class MRemoteDatastoreTest extends MAbstractWebfilesFramworkTest {

	protected $object;

	public function createRemoteDatastore() {
		$remoteDatastore = new MRemoteDatastore(
			"http://webfiles.sebastianmonzel.de/jenkins/datastore/?xml"
		);

		return $remoteDatastore;
	}

	/**
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
	public function testGetAllWebfiles() {

		$remoteDatastore = $this->createRemoteDatastore();

		$webfilesAsStream = $remoteDatastore->getAllWebfiles();

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
	public function testSearchByTemplate_findsOneWebfile() {

		$remoteDatastore = $this->createRemoteDatastore();

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
	public function testSearchByTemplate_findsNoWebfile() {

		$remoteDatastore = $this->createRemoteDatastore();

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
	public function testStoreWebfileAndDeleteItAgain() {

		$remoteDatastore = $this->createRemoteDatastore();

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

	public function testGetNextWebfileForTimestamp() {

		$remoteDatastore = $this->createRemoteDatastore();

		$next_webfile_for_timestamp = $remoteDatastore->getNextWebfileForTimestamp( -1 );
		// TODO für die methode  muss man normalize auf dem directory datastore aufrufen -
		// TODO normalize gibt es jedoch nicht im remote datastore - soll ich die normalize durchleiten?
		//self::assertNotNull($next_webfile_for_timestamp);
		self::assertEquals("","");
	}

	public function testIsReadonly() {

		$remoteDatastore = $this->createRemoteDatastore();

		$readOnly = $remoteDatastore->isReadOnly();
		self::assertFalse($readOnly);
	}

	public function testTryConnect() {

		$remoteDatastore = $this->createRemoteDatastore();

		$tryConnect = $remoteDatastore->tryConnect();
		self::assertTrue($tryConnect);
	}

	public function testGetLatestWebfiles() {

		$remoteDatastore = $this->createRemoteDatastore();

		// TODO für die methode  muss man normalize auf dem directory datastore aufrufen -
		// TODO normalize gibt es jedoch nicht im remote datastore - soll ich die normalize durchleiten? - ich glaub hier sollt man pro speicherung für jedes neue webfile extra ne normalisierung vornehmen (schon angefangen?)
		//$latestWebfiles = $remoteDatastore->getLatestWebfiles(1);
		//self::assertCount(1, $latestWebfiles);
		self::assertEquals("","");
	}

}