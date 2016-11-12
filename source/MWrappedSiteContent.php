<?php

namespace simpleserv\webfilesframework;

use simpleserv\webfilesframework\MSiteElement;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWrappedSiteContent extends MSiteElement
{

    public $m_sTitle;
    public $m_sIntroduction;
    public $m_sWrappedContentUrl;

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

    public function getIntroduction()
    {
        return $this->m_sIntroduction;
    }

    public function setIntroduction($introduction)
    {
        $this->m_sIntroduction = $introduction;
    }

    public function getWrappedContentUrl()
    {
        return $this->m_sWrappedContentUrl;
    }

    public function setWrappedContentUrl($wrappedContent)
    {
        $this->m_sWrappedContentUrl = $wrappedContent;
    }

}