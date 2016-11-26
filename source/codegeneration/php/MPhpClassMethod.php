<?php

namespace simpleserv\webfilesframework\core\codegeneration\php;

use simpleserv\webfilesframework\codegeneration\MAbstractClassMethod;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MPhpClassMethod extends MAbstractClassMethod
{


    public function __construct($visibility, $name, $content)
    {
        $this->visibility = $visibility;
        $this->name = $name;
        $this->content = $content;
    }


    public function generateCode()
    {

        $parameterCode = "";
        $parameterCount = 0;

        /* @var $parameter MPhpClassMethod */
        foreach ($this->parameters as $parameter) {

            $parameterCode .= $parameter->generateCode();

            if (count($this->parameters) > $parameterCount) {
                $parameterCode .= ",";
            }

            $parameterCount++;
        }

        $code = $this->visibility . " function " . $this->name . " (" . $parameterCode . ") {\n";
        $code .= $this->content;
        $code .= "}\n\n";
        return $code;
    }

    public function addParameter(MPhpClassMethodParameter $parameter)
    {
        $this->parameters[] = $parameter;
    }

}