<?php


use test\webfilesframework\core\datastore\types\MAbstractDatastoreTest;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datastore\types\rss\MRssDatastore;

class MRssDatastoreTest extends MAbstractDatastoreTest
{
    public function createRssDatastore() {
        return new MRssDatastore("https://www.spiegel.de/index.rss");
    }

    public function test_json_getAllWebfiles() {

        $jsonRemoteDatastore = $this->createRssDatastore();

        $webfilesAsStream = $jsonRemoteDatastore->getAllWebfiles();

        self::assertNotNull($webfilesAsStream);
        $webfilesArray = $webfilesAsStream->getArray();
        self::assertTrue(is_array($webfilesArray));
        self::assertCount(6, $webfilesArray);

        //var_dump($webfilesArray);

        /** @var MSampleWebfile $firstWebfile */
        $firstWebfile = $webfilesArray[0];

        self::assertEquals("Sebastian", $firstWebfile->getFirstname());
        self::assertEquals("Monzel", $firstWebfile->getLastname());
    }

}