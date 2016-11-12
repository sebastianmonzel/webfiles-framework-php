<?php

namespace simpleserv\webfilesframework\core\codegeneration;

use simpleserv\webfilesframework\MItem;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWebfileClassGeneration extends MItem
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