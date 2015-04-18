<?php

namespace simpleserv\webfilesframework\core\datastore;

use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;


/**
 * 
 * Transfers webfiles from one datastore to an other
 * @author Sebastian Monzel <mail@sebastianmonzel.de>
 */
class MDatastoreTransfer {
	
	private $source;
	private $target;
	
	function __construct(MAbstractDatastore $soure, MAbstractDatastore $target) {
		$this->source = $source;
		$this->target = $target;
	}
	
	function transfer() {
		
		if ( $this->target->isReadOnly() ) {
			throw new MDatastoreException("Cannot transfer data to a read-only datastore.");
		}
		
		$webfiles = $this->source->getWebfilesFromDatastore();

		foreach ($webfiles as $webfile) {
			$this->target->storeWebfile($webfile);
		}
	}
}