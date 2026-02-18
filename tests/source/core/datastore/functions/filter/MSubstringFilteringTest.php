<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datastore\functions\filter\MSubstringFiltering;

/**
 * @covers webfilesframework\core\datastore\functions\filter\MSubstringFiltering
 */
class MSubstringFilteringTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datastore\functions\filter\MSubstringFiltering::__construct
     * @covers webfilesframework\core\datastore\functions\filter\MSubstringFiltering::getValue
     */
    public function testConstruction() {
        $value = 'test';
        $filter = new MSubstringFiltering($value);
        
        $this->assertEquals($value, $filter->getValue());
    }

    /**
     * @covers webfilesframework\core\datastore\functions\filter\MSubstringFiltering::__construct
     * @covers webfilesframework\core\datastore\functions\filter\MSubstringFiltering::getValue
     */
    public function testGetValueWithDifferentTypes() {
        // Test with string
        $stringFilter = new MSubstringFiltering('search term');
        $this->assertEquals('search term', $stringFilter->getValue());

        // Test with number
        $numberFilter = new MSubstringFiltering(123);
        $this->assertEquals(123, $numberFilter->getValue());

        // Test with empty string
        $emptyFilter = new MSubstringFiltering('');
        $this->assertEquals('', $emptyFilter->getValue());
    }
}
