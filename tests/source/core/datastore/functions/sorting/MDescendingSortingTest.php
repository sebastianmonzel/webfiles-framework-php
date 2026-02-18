<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\functions\sorting\MDescendingSorting;

/**
 * @covers webfilesframework\core\datastore\functions\sorting\MDescendingSorting
 */
class MDescendingSortingTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datastore\functions\sorting\MDescendingSorting
     */
    public function testInstantiation() {
        $sorting = new MDescendingSorting();
        $this->assertInstanceOf(MDescendingSorting::class, $sorting);
    }
}
