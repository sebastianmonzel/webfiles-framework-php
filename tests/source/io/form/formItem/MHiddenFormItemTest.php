<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\form\formItem\MHiddenFormItem;

/**
 * @covers webfilesframework\io\form\formItem\MHiddenFormItem
 */
class MHiddenFormItemTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\form\formItem\MHiddenFormItem::init
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::__construct
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getCode
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getName
     */
    public function testHiddenFormItemCreation() {
        $formItem = new MHiddenFormItem('user_id', '123');
        
        $code = $formItem->getCode();
        $this->assertStringContainsString('type="hidden"', $code);
        $this->assertStringContainsString('name="user_id"', $code);
        $this->assertStringContainsString('value="123"', $code);
        
        $this->assertEquals('user_id', $formItem->getName());
    }

    /**
     * @covers webfilesframework\io\form\formItem\MHiddenFormItem::init
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getCode
     */
    public function testHiddenFormItemWithEmptyValue() {
        $formItem = new MHiddenFormItem('token', '');
        
        $code = $formItem->getCode();
        $this->assertStringContainsString('type="hidden"', $code);
        $this->assertStringContainsString('name="token"', $code);
    }
}
