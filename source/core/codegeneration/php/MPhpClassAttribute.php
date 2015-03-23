<?php

namespace simpleserv\webfilesframework\core\codegeneration\php;

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
class MPhpClassAttribute extends MAbstractClassAttribute {
	
	public function __construct($visibility, $name, $type) {
		$this->visibility = $visibility;
		$this->visibility = $name;
		$this->type = $type;
	}
	
	public function generateCode() {
		$code  = "	" . $this->visibility . " $" . $this->name . ";";
		return $code;
	}
	
}