<?php

namespace simpleserv\webfilesframework\core\time;

use simpleserv\webfilesframework\MItem;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWeekday extends MItem
{
    /** @var  string $name */
    private $name;

    public function __construct($id, $name)
    {
        $this->m_iId = $id;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }

}