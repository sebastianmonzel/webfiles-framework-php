<?php

namespace webfilesframework\core\datatype\time;

use webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWeekday extends MWebfile
{
    /** @var  string $name */
    private $m_sName;

    public function __construct($id, $name)
    {
        $this->m_iId = $id;
        $this->m_sName = $name;
    }

    public function getName()
    {
        return $this->m_sName;
    }

    public function setName($name)
    {
        $this->m_sName = $name;
    }

    public function __toString()
    {
        return $this->m_sName;
    }

}