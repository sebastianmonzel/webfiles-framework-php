<?php

namespace simpleserv\webfilesframework\core\datastore;

use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;
use simpleserv\webfilesframework\core\datastore\MDatastoreTransfer;

/**
 * Defines and provides basic functionality for caching a datastore.<br />
 * Basically the cached datastore is a combination between a efficient
 * datastore which can be used as cache and an slower datastore
 * (e.g. a directory datastore or a remote datastore).
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractCachableDatastore extends MAbstractDatastore
{

    /** @var MAbstractDatastore $cachingDatastore **/
    protected $cachingDatastore;

    /**
     * sets the datastore used for caching data from the original datastore.
     *
     * @param MAbstractDatastore $cachingDatastore
     * @throws \Exception
     */
    public function setCachingDatastore(MAbstractDatastore $cachingDatastore)
    {

        if ($cachingDatastore->isReadOnly()) {
            throw new MDatastoreException("Datastore for caching data cannot be readOnly.");
        }

        $this->cachingDatastore = $cachingDatastore;
    }

    public function fillCachingDatastore()
    {
        $datastoreTransfer = new MDatastoreTransfer($this, $this->cachingDatastore);
        $datastoreTransfer->transfer();
    }

    public function isDatastoreCached()
    {
        return isset($this->cachingDatastore);
    }

    public abstract function getLatestCachingTime();

    public abstract function isCacheActual();

}