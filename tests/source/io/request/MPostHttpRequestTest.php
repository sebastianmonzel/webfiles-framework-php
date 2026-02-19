<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\request\MPostHttpRequest;

/**
 * @covers webfilesframework\io\request\MPostHttpRequest
 */
class MPostHttpRequestTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\request\MPostHttpRequest::__construct
     * @covers webfilesframework\io\request\MPostHttpRequest::initContext
     * @covers webfilesframework\io\request\MAbstractHttpRequest::__construct
     */
    public function testConstructorWithoutData() {
        $request = new MPostHttpRequest('http://example.com');
        $this->assertInstanceOf(MPostHttpRequest::class, $request);
    }

    /**
     * @covers webfilesframework\io\request\MPostHttpRequest::__construct
     * @covers webfilesframework\io\request\MPostHttpRequest::initContext
     * @covers webfilesframework\io\request\MAbstractHttpRequest::__construct
     */
    public function testConstructorWithData() {
        $data = ['username' => 'test', 'password' => 'secret'];
        $request = new MPostHttpRequest('http://example.com', $data);
        $this->assertInstanceOf(MPostHttpRequest::class, $request);
    }
}
