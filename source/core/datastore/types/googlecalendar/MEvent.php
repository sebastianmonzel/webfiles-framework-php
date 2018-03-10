<?php

namespace simpleserv\webfilesframework\core\datastore\types\googlecalendar;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

class MEvent extends MWebfile
{
    /** @var string */
    private $m_sSummary;
    /** @var string */
    private $m_sDescription;


    /** @var string */
    private $m_sStart;
    /** @var string */
    private $m_sEnd;

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->m_sSummary;
    }

    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->m_sSummary = $summary;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->m_sDescription;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->m_sDescription = $description;
    }

    /**
     * @return string
     */
    public function getStart()
    {
        return $this->m_sStart;
    }

    /**
     * @param string $start
     */
    public function setStart($start)
    {
        $this->m_sStart = $start;
    }

    /**
     * @return string
     */
    public function getEnd()
    {
        return $this->m_sEnd;
    }

    /**
     * @param string $end
     */
    public function setEnd($end)
    {
        $this->m_sEnd = $end;
    }



}