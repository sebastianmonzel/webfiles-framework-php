<?php
/**
 * @covers \simpleserv\webfilesframework\core\datastore\types\database\MDatabaseDatastore
 * @covers MDatabaseConnection
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

        $databaseDatastore->storeWebfile(new \simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile());


        $result = $databaseDatastore->getWebfilesAsArray();

        self::assertEquals(1,count($result));

    }

    public function testNormalizeWebfiles()
    {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $databaseDatastore->storeWebfile(new \simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile());
        $databaseDatastore->normalize();

        $result = $databaseDatastore->getWebfilesAsArray();

        self::assertEquals(1,count($result));
    }

    public function testSearchByTemplate()
    {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $storedWebfileId = $databaseDatastore->storeWebfile(new \simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile());

        $template = new \simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile();
        $template->presetForTemplateSearch();
        $template->setId($storedWebfileId);

        $foundWebfiles = $databaseDatastore->searchByTemplate($template);

        self::assertEquals(1,count($foundWebfiles));
    }

    public function getLatestWebfiles() {

        $databaseDatastore = $this->createDatabaseDatastore();
        $databaseDatastore->deleteAll();

        $storedWebfile = $databaseDatastore->storeWebfile(new \simpleserv\webfilesframework\core\datastore\types\database\MSampleWebfile());

        $foundWebfiles = $databaseDatastore->getLatestWebfiles(1);

        self::assertEquals(1,count($foundWebfiles));
    }

    /**
     * @return \simpleserv\webfilesframework\core\datastore\types\database\MDatabaseDatastore
     */
    private function createDatabaseDatastore()
    {
        $connection = new \simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection(
            "sql11.freemysqlhosting.net",
            "sql11153903",
            "prefix_",
            "sql11153903",
            "saTTMEYWt4"); // yes i know it's the password of the database... ;) - you don't trick me. I trust you... :)

        $databaseDatastore = new \simpleserv\webfilesframework\core\datastore\types\database\MDatabaseDatastore($connection);
        return $databaseDatastore;
    }

}