<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\types\database\MDatabaseDatastoreException;

/**
 * @covers webfilesframework\core\datastore\types\database\MDatabaseDatastoreException
 */
class MDatabaseDatastoreExceptionTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datastore\types\database\MDatabaseDatastoreException::__construct
     */
    public function testConstruction() {
        $message = 'Database error occurred';
        $sql = 'SELECT * FROM users WHERE id = 1';
        $exception = new MDatabaseDatastoreException($message, $sql);
        
        $this->assertStringContainsString($message, $exception->getMessage());
        $this->assertStringContainsString($sql, $exception->getMessage());
        $this->assertInstanceOf(MDatabaseDatastoreException::class, $exception);
    }

    /**
     * @covers webfilesframework\core\datastore\types\database\MDatabaseDatastoreException::__construct
     */
    public function testThrowAndCatch() {
        $this->expectException(MDatabaseDatastoreException::class);
        $this->expectExceptionMessageMatches('/Query failed.*SELECT/');
        
        throw new MDatabaseDatastoreException('Query failed', 'SELECT * FROM invalid_table');
    }
}
