<?php

namespace simpleserv\webfilesframework;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MSiteContent extends MSiteElement
{

    public $m_sTitle;
    public $m_lContent;

    public static $m__sClassName = __CLASS__;

    public function __construct()
    {
        parent::__construct();
    }

    public function getTitle()
    {
        return $this->m_sTitle;
    }

    public function setTitle($p_sTitle)
    {
        $this->m_sTitle = $p_sTitle;
    }

    public function getContent()
    {
        return $this->m_lContent;
    }

    public function setContent($content)
    {
        $this->m_lContent = $content;
    }

    public function addContent($content)
    {
        $this->m_lContent .= $content;
    }

}