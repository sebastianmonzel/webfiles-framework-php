<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\io\request\MGetHttpRequest;

/**
 * @covers webfilesframework\io\request\MGetHttpRequest
 */
class MGetHttpRequestTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\io\request\MGetHttpRequest::__construct
     * @covers webfilesframework\io\request\MGetHttpRequest::initContext
     * @covers webfilesframework\io\request\MAbstractHttpRequest::__construct
     */
    public function testConstructorWithoutData() {
        $request = new MGetHttpRequest('http://example.com');
        $this->assertInstanceOf(MGetHttpRequest::class, $request);
    }

    /**
     * @covers webfilesframework\io\request\MGetHttpRequest::__construct
     * @covers webfilesframework\io\request\MGetHttpRequest::initContext
     * @covers webfilesframework\io\request\MAbstractHttpRequest::__construct
     */
    public function testConstructorWithData() {
        $data = ['param1' => 'value1', 'param2' => 'value2'];
        $request = new MGetHttpRequest('http://example.com', $data);
        $this->assertInstanceOf(MGetHttpRequest::class, $request);
    }
}
