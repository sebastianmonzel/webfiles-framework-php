<?php

namespace webfilesframework\codegeneration\php;

use webfilesframework\codegeneration\general\MAbstractClassMethodParameter;

/**
 * description
 *
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