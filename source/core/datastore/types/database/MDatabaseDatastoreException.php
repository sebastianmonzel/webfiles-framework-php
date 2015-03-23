<?php

namespace simpleserv\webfilesframework\core\datastore\types\database;

use \simpleserv\webfilesframework\core\datastore\MDatastoreException;

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