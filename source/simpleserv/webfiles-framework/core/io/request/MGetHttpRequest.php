<?php

namespace \simpleserv\webfiles-framework\core\io\request;

/**
 * 
 * @author semo
 *
 */
class MGetHttpRequest extends MAbstractHttpRequest {
	
	public function __construct($url,$data = null) {
		parent::__construct($url,$data);
	}
	
	public function initContext($data = null) {
	
		// use key 'http' even if you send the request to https://...
		$http = array();
		
		$http['header'] = "Content-type: application/x-www-form-urlencoded\r\n";
		$http['method'] = 'GET';
		if ( isset($data) ) {
			$http['content'] = $data;
		}
		
		$options = array(
				'http' => $http,
		);
		$this->context = stream_context_create($options);
	
	}

}