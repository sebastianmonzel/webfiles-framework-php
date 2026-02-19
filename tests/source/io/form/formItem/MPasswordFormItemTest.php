<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\form\formItem\MPasswordFormItem;

/**
 * @covers webfilesframework\io\form\formItem\MPasswordFormItem
 */
class MPasswordFormItemTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\form\formItem\MPasswordFormItem::init
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::__construct
     * @covers webfilesframework\io\form\formItem\MAbstractFormItem::getCode
     */
    public function testPasswordFormItemCreation() {
        $formItem = new MPasswordFormItem('password', '', 'Password');
        
        $code = $formItem->getCode();
        $this->assertStringContainsString('name="password"', $code);
        $this->assertStringContainsString('Password', $code);
        $this->assertStringContainsString('type="password"', $code);
    }
}
