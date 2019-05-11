<?php

namespace webfilesframework\core\datastore;

use webfilesframework\MWebfilesFrameworkException;

/**
 * General Exception used in datastores.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatastoreException extends MWebfilesFrameworkException
{

    public function __construct($message)
    {
        parent::__construct($message);
    }

}