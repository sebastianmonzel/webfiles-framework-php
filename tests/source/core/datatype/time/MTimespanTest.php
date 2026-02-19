<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datatype\time\MTimespan;

/**
 * @covers webfilesframework\core\datatype\time\MTimespan
 */
class MTimespanTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datatype\time\MTimespan::__construct
     * @covers webfilesframework\core\datatype\time\MTimespan::getStart
     * @covers webfilesframework\core\datatype\time\MTimespan::getEnd
     */
    public function testTimespanCreationAndGetters() {
        $start = 1000;
        $end = 2000;
        $timespan = new MTimespan($start, $end);
        
        $this->assertEquals($start, $timespan->getStart());
        $this->assertEquals($end, $timespan->getEnd());
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimespan::setStart
     * @covers webfilesframework\core\datatype\time\MTimespan::getStart
     */
    public function testTimespanSetStart() {
        $timespan = new MTimespan(100, 200);
        
        $newStart = 150;
        $timespan->setStart($newStart);
        $this->assertEquals($newStart, $timespan->getStart());
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimespan::setEnd
     * @covers webfilesframework\core\datatype\time\MTimespan::getEnd
     */
    public function testTimespanSetEnd() {
        $timespan = new MTimespan(100, 200);
        
        $newEnd = 250;
        $timespan->setEnd($newEnd);
        $this->assertEquals($newEnd, $timespan->getEnd());
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimespan::__construct
     * @covers webfilesframework\core\datatype\time\MTimespan::getStart
     * @covers webfilesframework\core\datatype\time\MTimespan::getEnd
     */
    public function testTimespanWithDateTimes() {
        $start = new DateTime('2024-01-01 10:00:00');
        $end = new DateTime('2024-01-01 12:00:00');
        $timespan = new MTimespan($start, $end);
        
        $this->assertSame($start, $timespan->getStart());
        $this->assertSame($end, $timespan->getEnd());
    }
}
