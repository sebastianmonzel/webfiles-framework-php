<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\form\formItem\MTextareaFormItem;

/**
 * @covers webfilesframework\io\form\formItem\MTextareaFormItem
 */
class MTextareaFormItemTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\form\formItem\MTextareaFormItem::init
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::__construct
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getCode
     */
    public function testTextareaFormItemCreation() {
        $formItem = new MTextareaFormItem('description', 'Some text', 'Description');
        
        $code = $formItem->getCode();
        $this->assertStringContainsString('name="description"', $code);
        $this->assertStringContainsString('Description', $code);
        $this->assertStringContainsString('dijit.form.Textarea', $code);
    }
}
