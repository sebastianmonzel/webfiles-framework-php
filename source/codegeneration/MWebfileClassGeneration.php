<?php

namespace simpleserv\webfilesframework\codegeneration;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWebfileClassGeneration
{

    public $webfileDefinition;


    public function __construct($webfileDefinition)
    {
        $this->webfileDefinition = $webfileDefinition;
    }


    public function generateCodeForProgrammingLangugage(MProgrammingLanguage $programmingLanguage)
    {
        $abstractClass = MCodeItemFactory::createClass($programmingLanguage);
        return $abstractClass->generateCode();
    }

}