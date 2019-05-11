<?php

namespace webfilesframework\codegeneration;

use webfilesframework\core\codegeneration\php\MPhpClassAttribute;
use webfilesframework\core\codegeneration\java\MJavaWebfileClass;
use webfilesframework\core\codegeneration\php\MPhpWebfileClass;
use webfilesframework\MWebfilesFrameworkException;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MCodeItemFactory
{

    /**
     * @param $programmingLanguage
     * @param $className
     * @return MJavaWebfileClass|MPhpWebfileClass
     * @throws MWebfilesFrameworkException
     */
    public static function createClass($programmingLanguage, $className)
    {

        if ($programmingLanguage == MProgrammingLanguage::PHP) {
            return new MPhpWebfileClass($className);
        } else if ($programmingLanguage == MProgrammingLanguage::JAVA) {
            return new MJavaWebfileClass($className);
        } else {
            throw new MWebfilesFrameworkException("Unknown programming language: " . $programmingLanguage);
        }
    }

    /**
     * @param string $programmingLanguage
     * @return MJavaWebfileClass|MPhpWebfileClass
     * @throws MWebfilesFrameworkException
     */
    public static function createClassAttribute($programmingLanguage)
    {
        if ($programmingLanguage == MProgrammingLanguage::PHP) {
            return new MPhpClassAttribute();
        } else if ($programmingLanguage == MProgrammingLanguage::JAVA) {
            return new MJavaClassAttribute();
        } else {
            throw new MWebfilesFrameworkException("Unknown programming language: " . $programmingLanguage);
        }
    }

}