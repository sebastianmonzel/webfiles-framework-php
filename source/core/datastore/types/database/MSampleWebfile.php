<?php

namespace simpleserv\webfilesframework\core\datastore\types\database;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

class MSampleWebfile extends MWebfile
{

    private $m_sFirstname;
    private $m_sLastname;
    private $m_sStreet;
    private $m_sHousenumber;
    private $m_sPostcode;
    private $m_sCity;

    public static $m__sClassName = __CLASS__;

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