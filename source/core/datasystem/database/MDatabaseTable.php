<?php

namespace simpleserv\webfilesframework\core\datasystem\database;

use \simpleserv\webfilesframework\MItem;
use \simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection;
use \simpleserv\webfilesframework\core\datasystem\database\MDatabaseTableColumn;

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
class MDatabaseTable {

	var $name = null;

	var $primaryKey = null;
	var $identifier = null;
	var $identifierSize = null;

	var $columns = array();
	
	var $databaseConnection;

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $name
	 */
	public function __construct(MDatabaseConnection $databaseConnection, $name) {
		
		$this->databaseConnection = $databaseConnection;
		$this->name = $name;
		
		/*$this->setIdentifier("id", "10");
		$this->addColumn("title",MIDbDatatypes::varchar(), 250);
		$this->addColumn("content",MIDbDatatypes::text());
		$this->addColumn("bla", MIDbDatatypes::int(),30);
		$this->addColumn("postActionId",MIDbDatatypes::int(), 20);*/
		
	}


	/**
	 *
	 * Enter description here ...
	 */
	public function create() {
		$query  = "CREATE TABLE IF NOT EXISTS `" . $this->name . "` (";
		
		if ( $this->identifier != null && $this->identifierSize != null ) {
			$query .= "`" . $this->identifier . "` int(" . $this->identifierSize . ") NOT NULL AUTO_INCREMENT,";
		}
		foreach ($this->columns as $value) {
			$query .= $value->getStringRepresentation();
		}
		
		if ( $this->primaryKey != null ) {
			$query .= "PRIMARY KEY (`" . $this->primaryKey . "`)";
		}
		$query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		
		$this->databaseConnection->query($query);
	}


	/**
	 *
	 * Enter description here ...
	 */
	public function drop() {
		$this->databaseConnection->query(
			"DROP TABLE IF EXISTS `" . $this->name . "`"
		);
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $colName
	 * @param unknown_type $size
	 */
	public function setIdentifier($colName, $size) {
		$this->identifier = $colName;
		$this->identifierSize = $size;
		$this->setPrimaryKey($colName);
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $colName
	 */
	public function setPrimaryKey($colName) {
		$this->primaryKey = $colName;
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $colName
	 * @param unknown_type $type
	 * @param unknown_type $size
	 */
	public function addColumn($name, $type, $length=null) {
		$column = new MDatabaseTableColumn($name, $type, $length);
		array_push($this->columns, $column);
	}

}