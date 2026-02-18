<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datasystem\database\MDatabaseTableColumn;
use webfilesframework\core\datasystem\database\MDatabaseDatatypes;
use webfilesframework\MWebfilesFrameworkException;

/**
 * @covers webfilesframework\core\datasystem\database\MDatabaseTableColumn
 */
class MDatabaseTableColumnTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datasystem\database\MDatabaseTableColumn::__construct
     * @covers webfilesframework\core\datasystem\database\MDatabaseTableColumn::getStringRepresentation
     */
    public function testVarcharColumn() {
        $column = new MDatabaseTableColumn('name', MDatabaseDatatypes::VARCHAR, 255);
        $result = $column->getStringRepresentation();
        
        $this->assertStringContainsString('name', $result);
        $this->assertStringContainsString('varchar(255)', $result);
        $this->assertStringContainsString('utf8', $result);
    }

    /**
     * @covers webfilesframework\core\datasystem\database\MDatabaseTableColumn::__construct
     * @covers webfilesframework\core\datasystem\database\MDatabaseTableColumn::getStringRepresentation
     */
    public function testTextColumn() {
        $column = new MDatabaseTableColumn('description', MDatabaseDatatypes::TEXT);
        $result = $column->getStringRepresentation();
        
        $this->assertStringContainsString('description', $result);
        $this->assertStringContainsString('text', $result);
        $this->assertStringContainsString('utf8', $result);
    }

    /**
     * @covers webfilesframework\core\datasystem\database\MDatabaseTableColumn::__construct
     * @covers webfilesframework\core\datasystem\database\MDatabaseTableColumn::getStringRepresentation
     */
    public function testIntColumn() {
        $column = new MDatabaseTableColumn('id', MDatabaseDatatypes::INT, 11);
        $result = $column->getStringRepresentation();
        
        $this->assertStringContainsString('id', $result);
        $this->assertStringContainsString('int(11)', $result);
    }

    /**
     * @covers webfilesframework\core\datasystem\database\MDatabaseTableColumn::__construct
     * @covers webfilesframework\core\datasystem\database\MDatabaseTableColumn::getStringRepresentation
     */
    public function testUnknownDatatypeThrowsException() {
        $this->expectException(MWebfilesFrameworkException::class);
        $this->expectExceptionMessage('Unknown Datatype');
        
        $column = new MDatabaseTableColumn('field', 'unknown_type');
        $column->getStringRepresentation();
    }
}
