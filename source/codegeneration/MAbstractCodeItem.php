<?php

namespace simpleserv\webfilesframework\codegeneration;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractCodeItem
{
    /**
     * Specifies a method which has to be implemented in all subclasses
     * to generate code for the given item.
     * @return string
     */
    public abstract function generateCode();
}