<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format\xml;

/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 */

/** 
 * description
 *
 * @package    de.simpleserv.core.xml
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
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