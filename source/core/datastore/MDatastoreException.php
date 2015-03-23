<?php

namespace simpleserv\webfilesframework\core\datastore;

/**
 * 
 * @author semo
 */
class MDatastoreException extends MException {
	
	public function __construct($message) {
		parent::__construct($message);
	}
	
}