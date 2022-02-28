<?php

namespace webfilesframework\core\datastore\types\rss;

use webfilesframework\core\datasystem\file\format\MWebfile;

class MRssFeedEntry extends MWebfile {

    private $m_sHeading;
    private $m_sDescription;

    private $m_sLink;

    /**
     * @return mixed
     */
    public function getHeading() {
        return $this->m_sHeading;
    }

    /**
     * @param mixed $m_sHeading
     */
    public function setHeading($m_sHeading)
    {
        $this->m_sHeading = $m_sHeading;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->m_sDescription;
    }

    /**
     * @param mixed $m_sDescription
     */
    public function setDescription($m_sDescription)
    {
        $this->m_sDescription = $m_sDescription;
    }

    /**
     * @return mixed
     */
    public function getLink() {
        return $this->m_sLink;
    }

    /**
     * @param mixed $m_sLink
     */
    public function setLink($m_sLink)
    {
        $this->m_sLink = $m_sLink;
    }

}