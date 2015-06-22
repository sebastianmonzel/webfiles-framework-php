<?php

namespace simpleserv\webfilesframework\core\codegeneration\php;

use simpleserv\webfilesframework\core\codegeneration\MAbstractClassMethod;

/**
 * description
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MPhpClassMethod extends MAbstractClassMethod {
	
	
	public function __construct($visibility,$name,$content) {
		$this->visibility = $visibility;
		$this->name = $name;
		$this->content = $content;
	}
	
	
	public function generateCode() {
		
		$parameterCode = "";
		$parameterCount = 0;
		$parametersSize = count($this->parameters);
		
		foreach ($this->parameters as $parameter) {
			
			$parameterCode .= $parameter->generateCode();
			
			if ( $parametersSize > $parameterCount ) {
				$parameterCode .= ",";
			}
			
			$parametersCount++;
		}
		
		$code  = $this->visibility . " function " . $this->methodName . " (" . $parameterCode . ") {\n";
		$code .= $this->content;
		$code .= "}\n\n";
		return $code;
	}
	
	public function addParameter(MPhpClassMethodParameter $paramter) {
		$this->parameters[] = $paramter;
	}
	
}