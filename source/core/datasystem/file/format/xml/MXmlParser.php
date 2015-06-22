<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format\xml;

/**
 * description
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MXmlParser {
	
	private $xmlElement;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function __construct($input) {
		$this->xmlElement = simplexml_load_string($input);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getXmlElement() {
		$this->xmlElement->children();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getChildren() {
		
	}
	
}