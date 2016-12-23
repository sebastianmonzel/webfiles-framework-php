<?php

namespace simpleserv\webfilesframework\codegeneration\php;

use simpleserv\webfilesframework\codegeneration\MAbstractClassMethodParameter;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MPhpClassMethodParameter extends MAbstractClassMethodParameter
{

    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function generateCode()
    {
        return $this->type . " $" . $this->name;
    }

}