<?php

namespace webfilesframework\core\datastore\types\database\resultHandler;


interface MIResultHandler
{

    /**
     * @return int
     */
    public function getResultSize();

    /**
     * @return object|\stdClass
     */
    public function fetchNextResultObject();

}