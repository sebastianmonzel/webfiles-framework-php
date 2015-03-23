<?php

namespace simpleserv\webfilesframework\core\codegeneration;

/**
 * 
 * @author semo
 *
 */
abstract class MAbstractClassMethodParameter extends MAbstractCodeItem {
	
	protected $name;
	protected $type;
	
	public function getName() {
	  return $this->name;
	}
	
	public function setName($name) {
	  $this->name = $name;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
}