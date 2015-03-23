<?php

namespace simpleserv\webfilesframework\core\datastore\types\database;

/**
 * 
 * @author semo
 */
class MDatabaseDatastoreException extends MDatastoreException {
	
	private $sql;
	
	public function __construct($message,$sql) {
		parent::__construct($message . "<br /><small>SQL: " . $sql . "</small>");
		$this->sql = $sql;
	}
	
}