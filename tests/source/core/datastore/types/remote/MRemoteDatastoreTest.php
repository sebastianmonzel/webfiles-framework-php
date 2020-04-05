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

	public function testGetWebfiles() {

		$remoteDatastore = $this->createRemoteDatastore();

		$webfilesAsStream = $remoteDatastore->getWebfilesAsStream();

		self::assertNotNull($webfilesAsStream);
		$webfilesArray = $webfilesAsStream->getWebfiles();
		self::assertTrue(is_array( $webfilesArray ));
		self::assertEquals(2, count( $webfilesArray ));

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

		self::assertEquals(1, count($webfilesArray));

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
		self::assertEquals(0, count($webfilesArray));
	}


	public function testStoreWebfileAndDeleteItAgain() {

		$remoteDatastore = $this->createRemoteDatastore();

		$webfileToStore = new MSampleWebfile();
		$webfileToStore->setLastname("Schmidt");

		$webfileToStore->setId(4);
		$webfilesStream = $remoteDatastore->storeWebfile($webfileToStore);

		$webfileToStore->setId(5);
		$webfilesStream = $remoteDatastore->storeWebfile($webfileToStore);
		self::assertEquals(4, count($webfilesStream->getWebfiles()));

		$searchtemplate = new MSampleWebfile();
		$searchtemplate->presetForTemplateSearch();
		$searchtemplate->setLastname("Schmidt");

		$webfilesStream = $remoteDatastore->deleteByTemplate($searchtemplate);
		self::assertEquals(2, count($webfilesStream->getWebfiles()));
	}

}