<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\MCombinedDatastore;
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;

/**
 * @covers webfilesframework\core\datastore\MCombinedDatastore
 */
class MCombinedDatastoreTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datastore\MCombinedDatastore::tryConnect
     */
    public function testTryConnect() {
        $datastore = new MCombinedDatastore();
        $this->assertTrue($datastore->tryConnect());
    }

    /**
     * @covers webfilesframework\core\datastore\MCombinedDatastore::isReadOnly
     */
    public function testIsReadOnly() {
        $datastore = new MCombinedDatastore();
        $this->assertTrue($datastore->isReadOnly());
    }

    /**
     * @covers webfilesframework\core\datastore\MCombinedDatastore::registerDatastore
     */
    public function testRegisterDatastore() {
        $combinedDatastore = new MCombinedDatastore();
        
        // Create a mock datastore to register
        $mockDatastore = $this->createMock(\webfilesframework\core\datastore\MAbstractDatastore::class);
        
        // This should not throw an exception
        $combinedDatastore->registerDatastore($mockDatastore);
        
        $this->assertTrue(true); // If we get here, registration worked
    }

    /**
     * @covers webfilesframework\core\datastore\MCombinedDatastore::getNextWebfileForTimestamp
     */
    public function testGetNextWebfileForTimestamp() {
        $datastore = new MCombinedDatastore();
        $result = $datastore->getNextWebfileForTimestamp(time());
        $this->assertNull($result);
    }
}
