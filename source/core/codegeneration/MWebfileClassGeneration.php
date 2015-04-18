<?php

namespace simpleserv\webfilesframework\core\codegeneration;

use simpleserv\webfilesframework\MItem;
/**
 * 
 * Enter description here ...
 * @author semo
 *
 */
class MWebfileClassGeneration extends MItem {
	
	public $webfileDefinition;
	
	
	public function __construct($webfileDefinition) {
		$this->webfileDefinition = $webfileDefinition;
	}
	
	
	public function generateCodeForProgrammingLangugage(MProgrammingLanguage $programmingLanguage) {
		$abstractClass = MCodeItemFactory::createClass($programmingLanguage);
		return $abstractClass->generateCode();
	}
	
}