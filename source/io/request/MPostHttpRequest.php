<?php

namespace simpleserv\webfilesframework\io\request;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MPostHttpRequest extends MAbstractHttpRequest
{

    public function __construct($url, $data = null)
    {
        parent::__construct($url, $data);
    }

    public function initContext($data = null)
    {

        // use key 'http' even if you send the request to https://...
        $http = array();

        $http['header'] = "Content-type: application/x-www-form-urlencoded\r\n";
        $http['method'] = 'POST';
        if (isset($data)) {
            $http['content'] = http_build_query($data);
        }

        $options = array(
            'http' => $http,
        );
        $this->context = stream_context_create($options);

    }

}