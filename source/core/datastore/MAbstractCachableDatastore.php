<?php

namespace simpleserv\webfilesframework\core\datastore;




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

    protected $latestCachingTime;

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

    public function getLatestCachingTime() {
        return $this->latestCachingTime;
    }

    /**
     * Define a criteria which indicates the actual datastore
     * with cache is not outdated.
     *
     * Default implemention checks if last caching was more than one day ago.
     *
     * @return mixed
     */
    public function isCacheActual() {

        if ( !isset($this->latestCachingTime) ) {
            return false;
        }

        return
            ((time() - $this->latestCachingTime)
                > (24 * 60 * 60));
    }

}