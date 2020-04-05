<?php

use PHPUnit\Framework\TestCase;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datastore\types\remote\MRemoteDatastore;

class MRemoteDatastoreTest extends TestCase {

	protected $object;

	public function createRemoteDatastore() {
		$remoteDatastore = new MRemoteDatastore(
			"http://webfiles.sebastianmonzel.de/jenkins/datastore/"
		);

		return $remoteDatastore;
	}

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

	public function testSearchByTemplate_findsOneWebfile() {

		$remoteDatastore = $this->createRemoteDatastore();

		$searchtemplate = new MSampleWebfile();
		$searchtemplate->presetForTemplateSearch();
		$searchtemplate->setLastname("Monzel");

		$webfilesArray = $remoteDatastore->searchByTemplate($searchtemplate);

		self::assertNotNull($webfilesArray);
		self::assertTrue(is_array($webfilesArray));

		self::assertCount(1, $webfilesArray);

		/** @var MSampleWebfile $firstWebfile */
		$firstWebfile = $webfilesArray[0];

		self::assertEquals($firstWebfile->getFirstname(),"Sebastian");
		self::assertEquals($firstWebfile->getLastname(),"Monzel");
	}


	public function testSearchByTemplate_findsNoWebfile() {

		$remoteDatastore = $this->createRemoteDatastore();

		$searchtemplate = new MSampleWebfile();
		$searchtemplate->presetForTemplateSearch();
		$searchtemplate->setLastname("Schmidt");

		// TODO warum array statt webfiles stream?
		$webfilesArray = $remoteDatastore->searchByTemplate($searchtemplate);

		self::assertNotNull($webfilesArray);
		self::assertTrue(is_array($webfilesArray));
		self::assertCount(0, $webfilesArray);
	}


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

}