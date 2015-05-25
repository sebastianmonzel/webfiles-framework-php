<?php

namespace simpleserv\webfilesframework\core\datastore\functions\filter;

use simpleserv\webfilesframework\core\datastore\functions\MIDatastoreFunction;

/**
 * 
 * @author semo
 *
 */
class MSubstringFiltering implements MIDatastoreFunction {
	
	protected $value;
	
	public function __construct($value) {
		
	}
	
	public function getValue() {
		return $this->value;
	}
	
}