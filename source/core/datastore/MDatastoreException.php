<?php

namespace simpleserv\webfilesframework\core\datastore;

use simpleserv\webfilesframework\core\exception\MException;

/**
 * description
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatastoreException extends MException {
	
	public function __construct($message) {
		parent::__construct($message);
	}
	
}