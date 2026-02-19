<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\form\formItem\MTextfieldFormItem;

/**
 * @covers webfilesframework\io\form\formItem\MTextfieldFormItem
 */
class MTextfieldFormItemTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\form\formItem\MTextfieldFormItem::init
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::__construct
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getCode
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getLabelWidth
     */
    public function testTextfieldFormItemCreation() {
        $formItem = new MTextfieldFormItem('username', 'john_doe', 'User Name');
        
        $code = $formItem->getCode();
        $this->assertStringContainsString('name="username"', $code);
        $this->assertStringContainsString('value="john_doe"', $code);
        $this->assertStringContainsString('User Name', $code);
        
        $this->assertEquals('username', $formItem->getName());
    }

    /**
     * @covers webfilesframework\io\form\formItem\MTextfieldFormItem::init
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::setLabelWidth
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getLabelWidth
     */
    public function testTextfieldFormItemLabelWidth() {
        $formItem = new MTextfieldFormItem('email', 'test@example.com');
        
        $this->assertEquals(180, $formItem->getLabelWidth());
        
        $formItem->setLabelWidth(200);
        $this->assertEquals(200, $formItem->getLabelWidth());
    }
}
