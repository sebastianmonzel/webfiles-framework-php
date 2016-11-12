<?php

namespace simpleserv\webfilesframework\core\codegeneration;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MAbstractClassMethod extends MAbstractCodeItem
{

    protected $visibility = "public";
    protected $name;
    protected $content;

    protected $parameters = array();


    public function getVisibility()
    {
        return $this->visibility;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContent()
    {
        return $this->content;
    }


}