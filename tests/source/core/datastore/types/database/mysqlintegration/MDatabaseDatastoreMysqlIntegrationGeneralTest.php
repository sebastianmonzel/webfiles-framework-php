<?php

use webfilesframework\core\datastore\types\database\MSampleWebfile;

/**
 * @covers webfilesframework\core\datastore\types\database\MDatabaseDatastore
 * @covers webfilesframework\core\datasystem\database\MDatabaseConnection
 *
 * Free Database by freemysqlhosting.net
 * Use http://www.phpmyadmin.co/ to look into the data of the database.
 */
class MDatabaseDatastoreMysqlIntegrationGeneralTest extends MAbstractDatastoreTest
{


	/**
	 * @throws ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\core\datastore\types\database\MDatabaseDatastoreException
	 */
	public function testGetWebfiles()
    {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $databaseDatastore->storeWebfile(new MSampleWebfile());

        $result = $databaseDatastore->getWebfilesAsArray();

        self::assertEquals(1,count($result));

    }


	/**
	 * @throws ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\core\datastore\types\database\MDatabaseDatastoreException
	 */
	public function testNormalizeWebfiles()
    {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $databaseDatastore->storeWebfile(new MSampleWebfile());
        $databaseDatastore->normalize();

        $result = $databaseDatastore->getWebfilesAsArray();

        self::assertEquals(1,count($result));
    }

	/**
	 * @throws ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\core\datastore\types\database\MDatabaseDatastoreException
	 */
	public function testSearchByTemplate()
    {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $storedWebfileId = $databaseDatastore->storeWebfile(new MSampleWebfile());

        $template = new MSampleWebfile();
        $template->presetForTemplateSearch();
        $template->setId($storedWebfileId);

        $foundWebfiles = $databaseDatastore->searchByTemplate($template);

        self::assertEquals(1,count($foundWebfiles));
    }

    public function testSearchByTemplateSorted() {
	    self::assertTrue(true); // dummy check to junit ignore warnings
    }

	/**
	 * @throws ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\core\datastore\types\database\MDatabaseDatastoreException
	 */
	public function testGetLatestWebfiles() {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $databaseDatastore->storeWebfile(new MSampleWebfile());

        $databaseDatastore->normalize();

        $foundWebfiles = $databaseDatastore->getLatestWebfiles(1);

        self::assertEquals(1,count($foundWebfiles));
    }

	/**
	 * @throws ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
	public function testUpdateWebfile() {
		self::assertTrue(true); // dummy check to junit ignore warnings

		$databaseDatastore = $this->createDatabaseDatastore();
		$databaseDatastore->deleteAll();
		$databaseDatastore->normalize();

	}


	/**
	 * @throws ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\core\datastore\types\database\MDatabaseDatastoreException
	 */
	public function testDeleteAllWebfiles() {

		$databaseDatastore = $this->createDatabaseDatastore();
		$databaseDatastore->deleteAll();
		$databaseDatastore->normalize();

		$foundWebfiles = $databaseDatastore->getLatestWebfiles(1);
		self::assertEquals(0,count($foundWebfiles));

		$databaseDatastore->storeWebfile(new MSampleWebfile());
		$databaseDatastore->normalize();
		$foundWebfiles = $databaseDatastore->getLatestWebfiles(1);
		self::assertEquals(1,count($foundWebfiles));

		$databaseDatastore->deleteAll();

		$databaseDatastore->normalize();
		$foundWebfiles = $databaseDatastore->getLatestWebfiles(1);
		self::assertEquals(0,count($foundWebfiles));
	}

	/**
	 * @throws ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\core\datastore\types\database\MDatabaseDatastoreException
	 */
	public function testDeleteWebfileByTemplate() {

		$databaseDatastore = $this->createDatabaseDatastore();
		$databaseDatastore->deleteAll();

		$databaseDatastore->storeWebfile($this->createSampleWebfile());
		$databaseDatastore->normalize();
		$foundWebfiles = $databaseDatastore->getLatestWebfiles();
		self::assertEquals(1,count($foundWebfiles));

		$databaseDatastore->deleteByTemplate($this->createSampleTemplate());

		$foundWebfiles = $databaseDatastore->getLatestWebfiles(1);
		$databaseDatastore->normalize();
		self::assertEquals(0,count($foundWebfiles));
	}

    /**
     * @return \webfilesframework\core\datastore\types\database\MDatabaseDatastore
     */
    private function createDatabaseDatastore()
    {
        $connection = new \webfilesframework\core\datasystem\database\MDatabaseConnection(
            "127.0.0.1",
            "webfiles",
            "prefix_",
            "root",
            "");

        $databaseDatastore = new \webfilesframework\core\datastore\types\database\MDatabaseDatastore($connection);
        return $databaseDatastore;
    }

}