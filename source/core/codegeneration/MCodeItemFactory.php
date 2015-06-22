<?php

namespace simpleserv\webfilesframework\core\codegeneration;

use simpleserv\webfilesframework\MItem;
use simpleserv\webfilesframework\core\codegeneration\java\MJavaWebfileClass;
use simpleserv\webfilesframework\core\codegeneration\php\MPhpWebfileClass;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
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