<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\MDatastoreException;

/**
 * @covers webfilesframework\core\datastore\MDatastoreException
 */
class MDatastoreExceptionTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datastore\MDatastoreException::__construct
     */
    public function testConstruction() {
        $message = 'Test datastore exception message';
        $exception = new MDatastoreException($message);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertInstanceOf(MDatastoreException::class, $exception);
    }

    /**
     * @covers webfilesframework\core\datastore\MDatastoreException::__construct
     */
    public function testThrowAndCatch() {
        $this->expectException(MDatastoreException::class);
        $this->expectExceptionMessage('Custom error message');
        
        throw new MDatastoreException('Custom error message');
    }
}
