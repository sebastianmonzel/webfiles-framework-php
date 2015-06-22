<?php

namespace simpleserv\webfilesframework\core\io\request;

/**
 * description
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MUrl {
	
	private $url;
	//parsed url splitted in: 
	//         PHP_URL_SCHEME, 
	//         PHP_URL_HOST, 
	//         PHP_URL_PORT, 
	//         PHP_URL_USER, 
	//         PHP_URL_PASS, 
	//         PHP_URL_PATH, 
	//         PHP_URL_QUERY and 
	//         PHP_URL_FRAGMENT
	//
	//@see http://php.net/manual/de/function.parse-url.php
	
	private $parsedUrlArray;
	private $params;
	
	private static $instance = null;
	
	public function __construct($url) {
		$this->url = $url;
		
		//split url
		$this->parsedUrlArray = parse_url($url);
		
		//split params
		if ( isset($this->parsedUrlArray['query']) ) {
			$query = $this->parsedUrlArray['query'];
			$queryParts = explode("&",$query);
			foreach ($queryParts as $value) {			
				$paramParts = explode("=", $value);
				if ( count($paramParts) == 2 ) {
					$key = $paramParts[0];
					$value = $paramParts[1];
					$this->params[$key] = $value;
				}
			}
		}
		
		//@todo verhalten dazuimplementieren, wenn urlteil nicht vorhanden sind. zum beispiel: fallback zu aufrufurl-teile
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return MUrl
	 */
	public static function getInstance() {
		if ( MUrl::$instance == null ) {
			MUrl::$instance = new MUrl($_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] . "?" . $_SERVER['QUERY_STRING']);
		}
		return MUrl::$instance;
	}
	
	public function getReferer() {
		return $_SERVER['HTTP_REFERER'];
	}
	
	public function getFullUrl() {
		return $this->url;
	}
	
	public function getParam($paramName) {
		if ( $this->paramExists($paramName) ) {
			return urldecode($this->params[$paramName]);
		} else {
			return NULL;
		}
		
	}
	
	/**
	 * Checks if the param exists in the query part of the url.
	 * @param MBoolean: true if param exists, false if not.
	 */
	public function paramExists($paramName) {
		return isset($this->params[$paramName]);
	}
	
	public function getParams() {
		return $this->params;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getPort() {
		return $this->parsedUrlArray['port'];
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getQueryString() {
		if ( isset($this->parsedUrlArray['query']) ) {
			return $this->parsedUrlArray['query'];
		} else {
			return "";
		}
	}
	
	
}
