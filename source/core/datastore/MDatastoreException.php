<?php

namespace simpleserv\webfilesframework\core\datastore;

use \simpleserv\webfilesframework\core\exception\MException;

/**
 * 
 * @author semo
 */
class MDatastoreException extends MException {
	
	public function __construct($message) {
		parent::__construct($message);
	}
	
}