<?php

namespace simpleserv\webfilesframework\core\io\request;

abstract class MAbstractHttpRequest {

	protected $url;
	protected $context;
	
	public function __construct($url,$data = null) {
		$this->url = $url;
		$this->initContext($data);
	}
	
	public function makeRequest() {
		return file_get_contents($this->url, false, $this->context);
	}
	
	public abstract function initContext($data);

}