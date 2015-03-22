<?php

namespace \simpleserv\webfiles-framework\core\codegeneration\php;

/**
 * 
 * @author semo
 *
 */
class MPhpClassMethodParameter extends MAbstractClassMethodParameter {
	
	public function __construct($name, $type) {
		$this->name = $name;
		$this->type = $type;
	}
	
	public function generateCode() {
		return $this->type . " $" . $this->name;
	}
	
}