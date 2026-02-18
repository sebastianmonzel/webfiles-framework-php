<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\functions\sorting\MAscendingSorting;

/**
 * @covers webfilesframework\core\datastore\functions\sorting\MAscendingSorting
 */
class MAscendingSortingTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datastore\functions\sorting\MAscendingSorting
     */
    public function testInstantiation() {
        $sorting = new MAscendingSorting();
        $this->assertInstanceOf(MAscendingSorting::class, $sorting);
    }
}
