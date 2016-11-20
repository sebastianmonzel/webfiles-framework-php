<?php

namespace simpleserv\webfilesframework\core\authentication;


/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MNotEnoughRightsException extends \Exception
{

    public function __construct($code = 0)
    {
        parent::__construct($code);
    }

    public function __toString()
    {
        return __CLASS__;
    }
}