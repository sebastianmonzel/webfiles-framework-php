<?php

use PHPUnit\Framework\TestCase;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datastore\types\remote\MRemoteDatastore;

class MRemoteDatastoreTest extends TestCase {

	protected $object;

	public function createRemoteDatastore() {
		$remoteDatastore = new MRemoteDatastore(
			"http://webfiles.sebastianmonzel.de/datastore/"
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

		self::assertEquals($firstWebfile->getFirstname(),"Sebastian");
		self::assertEquals($firstWebfile->getLastname(),"Monzel");

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

		$webfilesArray = $remoteDatastore->searchByTemplate($searchtemplate);

		self::assertNotNull($webfilesArray);
		self::assertTrue(is_array($webfilesArray));
		self::assertEquals(0, count($webfilesArray));
	}

}