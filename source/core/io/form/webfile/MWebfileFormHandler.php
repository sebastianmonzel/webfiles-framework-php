<?php

namespace \simpleserv\webfiles-framework\core\io\form\webfile;

/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 *
 */

/**
 * description
 *
 * @package    de.simpleserv.core.form.handler
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MWebfileFormHandler {
	
	var $requestArray;
	/**
	 * 
 	 */
	public function __construct($requestArray) {
		$this->requestArray = $requestArray;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getWebfileFromRequestArray() {
		
		$lastPositionOfPoint = strrpos ( $this->requestArray['classname'] , "." );
		if ( $lastPositionOfPoint != false ) {
			$lastPositionOfPoint++;
		} else {
			$lastPositionOfPoint = 0;
		}
		
		$classnameWithoutPackagePath = substr($this->requestArray['classname'], $lastPositionOfPoint);

		$item = new $classnameWithoutPackagePath;
		$attributes = $item->getAttributes();
		
		foreach ($attributes as $attribute) {
 			$attributeName = $attribute->getName();
 			if ( isset($this->requestArray[$attributeName]) ) {
 				$attribute->setAccessible(true);
 				$attribute->setValue($item,$this->requestArray[$attributeName]);
 			}
    	}
    	return $item;
	}
	
}