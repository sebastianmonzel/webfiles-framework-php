<?php

namespace simpleserv\webfilesframework\core\datastore;

use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;
use simpleserv\webfilesframework\core\datastore\MDatastoreTransfer;

/**
 * 
 * @author semo
 *
 */
abstract class MAbstractCachableDatastore extends MAbstractDatastore {
	
	protected $cachingDatastore;
	
	/**
	 * sets the datastore used for caching data from the original datastore.
	 * 
	 * @param MAbstractDatastore $cachingDatastore
	 * @throws Exception
	 */
	public function setCachingDatastore(MAbstractDatastore $cachingDatastore) {
		
		if ( $cachingDatastore->isReadOnly() ) {
			throw new Exception("Datastore for caching data cannot be readOnly.");
		}
		
		$this->cachingDatastore = $cachingDatastore;
	}
	
	public function fillCachingDatastore() {
		$datastoreTransfer = new MDatastoreTransfer($this, cachingDatastore);
		$datastoreTransfer->transfer();
	}
	
	public function isDatastoreCached() {
		return isset($this->cachingDatastore);
	}
	
	public abstract function getLatestCachingTime();
	
	public abstract function isCacheActual();
	
}