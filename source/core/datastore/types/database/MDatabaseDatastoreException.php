<?php

namespace simpleserv\webfilesframework\core\datastore\types\database;

use simpleserv\webfilesframework\core\datastore\MDatastoreException;

/**
 * General Exception used in database datastore.
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatabaseDatastoreException extends MDatastoreException {
	
	private $sql;
	
	public function __construct($message,$sql) {
		parent::__construct($message . "<br /><small>SQL: " . $sql . "</small>");
		$this->sql = $sql;
	}
	
}