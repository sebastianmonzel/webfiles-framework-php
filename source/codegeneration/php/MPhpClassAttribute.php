<?php

namespace webfilesframework\codegeneration\php;

use webfilesframework\codegeneration\MAbstractClassAttribute;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MPhpClassAttribute extends MAbstractClassAttribute
{

    public function __construct($visibility, $name, $type)
    {
        $this->visibility = $visibility;
        $this->visibility = $name;
        $this->type = $type;
    }

    public function generateCode()
    {
        $code = "	" . $this->visibility . " $" . $this->name . ";";
        return $code;
    }

}