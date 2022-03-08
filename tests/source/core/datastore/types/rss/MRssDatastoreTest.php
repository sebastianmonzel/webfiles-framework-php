<?php


use test\webfilesframework\core\datastore\types\MAbstractDatastoreTest;
use webfilesframework\core\datastore\types\database\MSampleWebfile;
use webfilesframework\core\datastore\types\rss\MRssDatastore;
use webfilesframework\core\datastore\types\rss\MRssFeedEntry;

class MRssDatastoreTest extends MAbstractDatastoreTest
{
    public function test_json_getAllWebfiles_spiegel() {

        $jsonRemoteDatastore = new MRssDatastore("https://www.spiegel.de/index.rss");

        $webfilesAsStream = $jsonRemoteDatastore->getAllWebfiles();

        self::assertNotNull($webfilesAsStream);
        $webfilesArray = $webfilesAsStream->getArray();
        self::assertTrue(is_array($webfilesArray));
        self::assertCount(6, $webfilesArray);

        //var_dump($webfilesArray);

        /** @var MRssFeedEntry $firstWebfile */
        $firstWebfile = reset($webfilesArray);

        self::assertEquals($firstWebfile->getHeading(), $firstWebfile->getHeading()); // TODO
    }

    public function test_json_getAllWebfiles_tagesschau() {

        $jsonRemoteDatastore = new MRssDatastore("https://www.tagesschau.de/xml/rss2/");
        $webfilesAsStream = $jsonRemoteDatastore->getAllWebfiles();

        self::assertNotNull($webfilesAsStream);
        $webfilesArray = $webfilesAsStream->getArray();
        self::assertTrue(is_array($webfilesArray));
        self::assertCount(6, $webfilesArray);


        /** @var MRssFeedEntry $firstWebfile */
        var_dump($webfilesArray);


        $firstWebfile = reset($webfilesArray);

        self::assertEquals($firstWebfile->getHeading(), $firstWebfile->getHeading()); // TODO
    }

}