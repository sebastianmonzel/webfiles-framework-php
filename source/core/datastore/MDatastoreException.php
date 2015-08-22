<?php

namespace simpleserv\webfilesframework\core\datastore;

use simpleserv\webfilesframework\MWebfilesFrameworkException;

/**
 * General Exception used in datastores.
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatastoreException extends MWebfilesFrameworkException {
	
	public function __construct($message) {
		parent::__construct($message);
	}
	
}