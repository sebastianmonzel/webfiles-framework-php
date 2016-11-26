<?php

namespace simpleserv\webfilesframework\core\datastore\types\database\resultHandler;


class MMysqlResultHandler implements MIResultHandler
{
    /**
     * @var \mysqli_result
     */
    var $result;

    /**
     * MMysqlResultHandler constructor.
     * @param \mysqli_result $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }


    /**
     * @return int
     */
    public function getResultSize(): int
    {
        return $this->result->num_rows;
    }

    /**
     * @return object|\stdClass
     */
    public function fetchNextResultObject() {
        $nextResultObject = $this->result->fetch_object();
        return $nextResultObject;
    }


}