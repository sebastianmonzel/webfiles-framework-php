<?php

use simpleserv\webfilesframework\core\datastore\MDatastoreFactory;

/**
 * Test class for MDatastoreFactory.
 * Generated by PHPUnit on 2015-04-11 at 17:49:50.
 */
class MWebfileTransferTest extends PHPUnit_Framework_TestCase {
    /**
     * @var MDatastoreFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MDatastoreFactory();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers MDatastoreFactory::createDatastore
     * @todo Implement testCreateDatastore().
     */
    public function testCreateDatastore()
    {
        echo "bla";
    }
}
