<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\form\formItem\MDateFormItem;

/**
 * @covers webfilesframework\io\form\formItem\MDateFormItem
 */
class MDateFormItemTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\form\formItem\MDateFormItem::init
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::__construct
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getCode
     */
    public function testDateFormItemCreation() {
        $formItem = new MDateFormItem('birthdate', '2024-01-01', 'Date of Birth');
        
        $code = $formItem->getCode();
        $this->assertStringContainsString('name="birthdate"', $code);
        $this->assertStringContainsString('value="2024-01-01"', $code);
        $this->assertStringContainsString('Date of Birth', $code);
        $this->assertStringContainsString('dijit.form.DateTextBox', $code);
    }
}
