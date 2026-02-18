<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\form\formItem\MTimeFormItem;

/**
 * @covers webfilesframework\io\form\formItem\MTimeFormItem
 */
class MTimeFormItemTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\form\formItem\MTimeFormItem::init
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::__construct
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getCode
     */
    public function testTimeFormItemCreation() {
        $formItem = new MTimeFormItem('meeting_time', '14:30', 'Meeting Time');
        
        $code = $formItem->getCode();
        $this->assertStringContainsString('name="meeting_time"', $code);
        $this->assertStringContainsString('value="14:30"', $code);
        $this->assertStringContainsString('Meeting Time', $code);
        $this->assertStringContainsString('dijit.form.TimeTextBox', $code);
    }
}
