<?php

namespace webfilesframework\core\datastore\types\database;

use webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * TODO extract as test only class
 * Class MSampleWebfile
 * @package webfilesframework\core\datastore\types\database
 */
class MSampleWebfile extends MWebfile
{

    private $m_sFirstname;
    private $m_sLastname;
    private $m_sStreet;
    private $m_sHousenumber;
    private $m_sPostcode;
    private $m_sCity;

    /**
     * @param mixed $m_sFirstname
     */
    public function setFirstname($m_sFirstname)
    {
        $this->m_sFirstname = $m_sFirstname;
    }

    /**
     * @param mixed $m_sLastname
     */
    public function setLastname($m_sLastname)
    {
        $this->m_sLastname = $m_sLastname;
    }

    /**
     * @param mixed $m_sStreet
     */
    public function setStreet($m_sStreet)
    {
        $this->m_sStreet = $m_sStreet;
    }

    /**
     * @param mixed $m_sHousenumber
     */
    public function setHousenumber($m_sHousenumber)
    {
        $this->m_sHousenumber = $m_sHousenumber;
    }

    /**
     * @param mixed $m_sPostcode
     */
    public function setPostcode($m_sPostcode)
    {
        $this->m_sPostcode = $m_sPostcode;
    }

    /**
     * @param mixed $m_sCity
     */
    public function setCity($m_sCity)
    {
        $this->m_sCity = $m_sCity;
    }


}