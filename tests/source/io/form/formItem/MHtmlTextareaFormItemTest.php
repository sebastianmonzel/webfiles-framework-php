<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\form\formItem\MHtmlTextareaFormItem;

/**
 * @covers webfilesframework\io\form\formItem\MHtmlTextareaFormItem
 */
class MHtmlTextareaFormItemTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\form\formItem\MHtmlTextareaFormItem::init
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::__construct
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getCode
     */
    public function testHtmlTextareaFormItemCreation() {
        $formItem = new MHtmlTextareaFormItem('content', '<p>HTML content</p>', 'HTML Content');
        
        $code = $formItem->getCode();
        $this->assertStringContainsString('name="content"', $code);
        $this->assertStringContainsString('HTML Content', $code);
    }
}
