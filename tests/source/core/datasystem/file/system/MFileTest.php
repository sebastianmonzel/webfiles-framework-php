<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datasystem\file\system\MFile;

/**
 * @covers webfilesframework\core\datasystem\file\system\MFile
 */
class MFileTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::containsFileseperator
     */
    public function testContainsFileseperatorWithForwardSlash() {
        $file = new MFile('/tmp/test.txt');
        $this->assertTrue($file->containsFileseperator('/path/to/file.txt'));
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::containsFileseperator
     */
    public function testContainsFileseperatorWithBackslash() {
        $file = new MFile('/tmp/test.txt');
        $this->assertTrue($file->containsFileseperator('C:\\path\\to\\file.txt'));
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::containsFileseperator
     */
    public function testContainsFileseperatorWithoutSeperator() {
        $file = new MFile('/tmp/test.txt');
        $this->assertFalse($file->containsFileseperator('filename.txt'));
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::extractFileName
     */
    public function testExtractFileNameWithForwardSlash() {
        $this->assertEquals('file.txt', MFile::extractFileName('/path/to/file.txt'));
        $this->assertEquals('test.php', MFile::extractFileName('/home/user/test.php'));
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::extractFileName
     */
    public function testExtractFileNameWithBackslash() {
        $this->assertEquals('file.txt', MFile::extractFileName('C:\\path\\to\\file.txt'));
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::extractFileName
     */
    public function testExtractFileNameWithoutPath() {
        $this->assertEquals('file.txt', MFile::extractFileName('file.txt'));
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::extractFolderName
     */
    public function testExtractFolderNameWithForwardSlash() {
        $this->assertEquals('/path/to/', MFile::extractFolderName('/path/to/file.txt'));
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::extractFolderName
     */
    public function testExtractFolderNameWithBackslash() {
        $this->assertEquals('C:\\path\\to\\', MFile::extractFolderName('C:\\path\\to\\file.txt'));
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::extractFolderName
     */
    public function testExtractFolderNameWithoutPath() {
        $this->assertEquals('file.txt', MFile::extractFolderName('file.txt'));
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::__construct
     * @covers webfilesframework\core\datasystem\file\system\MFile::writeContent
     * @covers webfilesframework\core\datasystem\file\system\MFile::getContent
     * @covers webfilesframework\core\datasystem\file\system\MFile::exists
     */
    public function testWriteAndReadContent() {
        $tmpFile = '/tmp/test_webfiles_' . time() . '.txt';
        $file = new MFile($tmpFile);
        
        $content = 'Test content for file';
        $file->writeContent($content);
        
        $this->assertTrue($file->exists());
        $this->assertEquals($content, $file->getContent());
        
        // Cleanup
        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }
    }

    /**
     * @covers webfilesframework\core\datasystem\file\system\MFile::__construct
     * @covers webfilesframework\core\datasystem\file\system\MFile::writeContent
     * @covers webfilesframework\core\datasystem\file\system\MFile::getContent
     */
    public function testWriteContentWithOverwrite() {
        $tmpFile = '/tmp/test_webfiles_overwrite_' . time() . '.txt';
        $file = new MFile($tmpFile);
        
        $file->writeContent('First content');
        $file->writeContent('Second content', true);
        
        $this->assertEquals('Second content', $file->getContent());
        
        // Cleanup
        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }
    }
}
