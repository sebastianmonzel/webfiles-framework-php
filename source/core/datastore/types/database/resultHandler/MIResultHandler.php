<?php
/**
 * Created by PhpStorm.
 * User: semo
 * Date: 21.11.2016
 * Time: 21:38
 */

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