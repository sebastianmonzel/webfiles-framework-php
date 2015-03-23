<?php

namespace \simpleserv\webfiles-framework\core\codegeneration\php;

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
 * @package    de.simpleserv.core.abstraction.code
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MPhpWebfileClass extends MAbstractClass {
	
	public function generateCode() {
		
		$this->generateSetterAndGetter();
		
		$code = parent::generateCode();
		return $code;
	}
	
	protected function generatePreambleCode() {
		return "<?php \n";
	}
	
	protected function generateHeaderCode() {
		
		
		return "class " . $this->className . " extends MWebfile { \n\n";
	}
	
	protected function generateAttributes() {
		
		$code = "";
		$attribute = new MPhpClassAttribute();
		
		foreach ($this->attributes as $attribute) {
			
			if ( ! $attribute instanceof MPhpClassAttribute) {
				throw new MException("Cannot generate code for attribute which is not of type 'MPhpClassAttribute'.");
			}
			
			// ADD "m_" BEFORE THE ATTRIBUTENAME TO MAKE IT POSSIBLE FOR WEBFILES FRAMEWORK TO RECOGNIZE RELEVANT ATTRIBUTES
			$modifiedAttribute = new MPhpClassAttribute($attribute->getVisibility(), "m_" . $attribute->getName(), $attribute->getType());
			
			$code .= $modifiedAttribute->generateCode() . "\n";
		}
		
		return $code;
	}
	
	protected function generateFooterCode() {
		return "}";
	}
	
	public function addAttribute(MPhpClassAttribute $attribute) {
		$this->attributes[] = $attribute;
	}
	
	public function addMethod(MPhpClassMethod $method) {
		$this->methods[] = $method;
	}
	
	private function generateSetterAndGetter() {
		foreach ($this->attributes as $attribute) {
			
			$firstLetterUppercaseAttribute = ucfirst($attribute);
			
			// GETTER
			$getterMethodName = "get" . $firstLetterUppercaseAttribute;
			$getterContent = "return \$this->m_" . $attribute . ";";
			$setterMethod = new MPhpClassMethod("public", $getterMethodName, $getterContent);
			$this->addMethod($getterMethodName);
			
			// SETTER
			$setterMethodName = "set" . $firstLetterUppercaseAttribute;
			$setterContent = "\$this->m_" . $attribute . " = $" . $attribute . ";";
			$setterMethod = new MPhpClassMethod("public", $setterMethodName, $setterContent);
			$setterMethod->addParameter(new MPhpClassMethodParameter($attribute, ""));
			$this->addMethod($setterMethod);
			
		}
	}
}