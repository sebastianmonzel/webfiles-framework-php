<?php

namespace \simpleserv\webfiles-framework\core\codegeneration;

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
			return new MPhpClass($className);
		} else if ( $programmingLanguage == "java" ) {
			return new MJavaClass($className);
		}
		
	}
	
	public static function createClassAttribute($programmingLanguage) {
		
		if ( $programmingLanguage == "php" ) {
			return new MPhpClass();
		} else if ( $programmingLanguage == "java" ) {
			return new MJavaClass();
		}
		
	}
	
}