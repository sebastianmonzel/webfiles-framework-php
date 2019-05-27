<?php
/**
 * @covers webfilesframework\core\datastore\types\database\MDatabaseDatastore
 * @covers webfilesframework\core\datasystem\database\MDatabaseConnection
 *
 * Free Database by freemysqlhosting.net
 * Use http://www.phpmyadmin.co/ to look into the data of the database.
 */
class MDatabaseDatastoreMysqlIntegrationTest extends PHPUnit_Framework_TestCase
{


    public function testGetWebfiles()
    {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $databaseDatastore->storeWebfile(new \webfilesframework\core\datastore\types\database\MSampleWebfile());


        $result = $databaseDatastore->getWebfilesAsArray();

        self::assertEquals(1,count($result));

    }

    public function testNormalizeWebfiles()
    {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $databaseDatastore->storeWebfile(new \webfilesframework\core\datastore\types\database\MSampleWebfile());
        $databaseDatastore->normalize();

        $result = $databaseDatastore->getWebfilesAsArray();

        self::assertEquals(1,count($result));
    }

    public function testSearchByTemplate()
    {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $storedWebfileId = $databaseDatastore->storeWebfile(new \webfilesframework\core\datastore\types\database\MSampleWebfile());

        $template = new \webfilesframework\core\datastore\types\database\MSampleWebfile();
        $template->presetForTemplateSearch();
        $template->setId($storedWebfileId);

        $foundWebfiles = $databaseDatastore->searchByTemplate($template);

        self::assertEquals(1,count($foundWebfiles));
    }

    public function testGetLatestWebfiles() {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $storedWebfile = $databaseDatastore->storeWebfile(new \webfilesframework\core\datastore\types\database\MSampleWebfile());

        $foundWebfiles = $databaseDatastore->getLatestWebfiles(1);

        self::assertEquals(1,count($foundWebfiles));
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