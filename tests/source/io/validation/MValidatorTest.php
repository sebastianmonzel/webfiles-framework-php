<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\form\validation\MValidator;

/**
 * @covers webfilesframework\io\form\validation\MValidator
 */
class MValidatorTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\form\validation\MValidator::validateUrlParam
     */
    public function testValidateUrlParam() {
        $this->assertEquals('test', MValidator::validateUrlParam('test'));
        $this->assertEquals('&lt;script&gt;', MValidator::validateUrlParam('<script>'));
        $this->assertEquals('&amp;', MValidator::validateUrlParam('&'));
        $this->assertEquals('&quot;', MValidator::validateUrlParam('"'));
        $this->assertEquals('&#039;', MValidator::validateUrlParam("'"));
    }

    /**
     * @covers webfilesframework\io\form\validation\MValidator::cutLongWords
     */
    public function testCutLongWords() {
        // Test with default length (25)
        $shortText = 'This is a short text';
        $this->assertEquals($shortText, MValidator::cutLongWords($shortText));

        // Test with long word that should be split
        $longWord = 'ThisIsAVeryLongWordThatShouldBeSplit';
        $result = MValidator::cutLongWords($longWord, 10);
        $this->assertNotEquals($longWord, $result);
        $this->assertStringContainsString(' ', $result);

        // Test with custom length
        $text = 'normalword verylongword';
        $result = MValidator::cutLongWords($text, 8);
        $this->assertStringContainsString(' ', $result);
    }
}
