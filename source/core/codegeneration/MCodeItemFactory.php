<?php

namespace simpleserv\webfilesframework\core\codegeneration;

use simpleserv\webfilesframework\MItem;
use simpleserv\webfilesframework\core\codegeneration\java\MJavaWebfileClass;
use simpleserv\webfilesframework\core\codegeneration\php\MPhpWebfileClass;
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
class MCodeItemFactory extends MItem {
	
	
	public static function createClass($programmingLanguage,$namespace,$className) {
		
		if ( $programmingLanguage == "php" ) {
			return new MPhpWebfileClass($className);
		} else if ( $programmingLanguage == "java" ) {
			return new MJavaWebfileClass($className);
		}
		
	}
	
	public static function createClassAttribute($programmingLanguage) {
		
		if ( $programmingLanguage == "php" ) {
			return new MPhpWebfileClass();
		} else if ( $programmingLanguage == "java" ) {
			return new MJavaWebfileClass();
		}
		
	}
	
}