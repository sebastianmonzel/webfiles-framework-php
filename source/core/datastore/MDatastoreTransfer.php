<?php

namespace webfilesframework\core\datastore;



/**
 * Transfers webfiles from one datastore to another.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatastoreTransfer
{

    private $source;
    private $target;

    function __construct(MAbstractDatastore $source, MAbstractDatastore $target)
    {
        if ($source == null) {
            throw new MDatastoreException("source datastore cannot be null.");
        }
        if ($target == null) {
            throw new MDatastoreException("target datastore cannot be null.");
        }

        $this->source = $source;
        $this->target = $target;
    }

    function transfer()
    {

        if ($this->target->isReadOnly()) {
            throw new MDatastoreException("Cannot transfer data to a read-only datastore.");
        }

        $webfiles = $this->source->getWebfilesAsStream();
        $this->target->storeWebfilesFromStream($webfiles);
    }
}