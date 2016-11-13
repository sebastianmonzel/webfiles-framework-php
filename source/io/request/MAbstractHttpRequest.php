<?php

namespace simpleserv\webfilesframework\io\request;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractHttpRequest
{

    protected $url;
    protected $context;

    public function __construct($url, $data = null)
    {
        $this->url = $url;
        $this->initContext($data);
    }

    public function makeRequest()
    {
        return file_get_contents($this->url, false, $this->context);
    }

    public abstract function initContext($data);

}