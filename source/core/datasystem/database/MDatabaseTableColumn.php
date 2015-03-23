<?php

namespace simpleserv\webfilesframework\core\datasystem\database;

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
 * @package    de.simpleserv.core.database
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MDatabaseTableColumn
{
	
	var $name;
	var $type;
	var $length;
	
	public function __construct($name, $type, $length = null) {
		$this->name = $name;
		$this->type = $type;
		$this->length = $length;
	}
	
	public function getStringRepresentation() {
		
		if ( $this->type == "varchar" ) {
			return "`" . $this->name . "` varchar(" . $this->length . ") CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,";
		} elseif ( $this->type == "text" ) {
			return "`" . $this->name . "` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,";
		} elseif ( $this->type == "int" ) {
			return "`" . $this->name . "` int(" . $this->length . ") NOT NULL,";
		}
		
	}
	
}
