<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datatype\time\MWeekday;

/**
 * @covers webfilesframework\core\datatype\time\MWeekday
 */
class MWeekdayTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datatype\time\MWeekday::__construct
     * @covers webfilesframework\core\datatype\time\MWeekday::getName
     * @covers webfilesframework\core\datatype\time\MWeekday::setName
     * @covers webfilesframework\core\datatype\time\MWeekday::__toString
     */
    public function testWeekdayCreationAndGetters() {
        $weekday = new MWeekday(1, 'Monday');
        
        $this->assertEquals('Monday', $weekday->getName());
        $this->assertEquals('Monday', (string)$weekday);
    }

    /**
     * @covers webfilesframework\core\datatype\time\MWeekday::setName
     * @covers webfilesframework\core\datatype\time\MWeekday::getName
     */
    public function testWeekdaySetName() {
        $weekday = new MWeekday(2, 'Tuesday');
        
        $weekday->setName('Wednesday');
        $this->assertEquals('Wednesday', $weekday->getName());
    }

    /**
     * @covers webfilesframework\core\datatype\time\MWeekday::__toString
     */
    public function testWeekdayToString() {
        $weekday = new MWeekday(5, 'Friday');
        
        $this->assertEquals('Friday', (string)$weekday);
        $this->assertIsString((string)$weekday);
    }
}
