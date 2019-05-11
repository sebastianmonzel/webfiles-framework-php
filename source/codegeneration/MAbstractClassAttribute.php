<?php

namespace simpleserv\webfilesframework\codegeneration;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractClassAttribute extends MAbstractCodeItem
{

    protected $visibility = "public";
    protected $name;
    protected $type;

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

}