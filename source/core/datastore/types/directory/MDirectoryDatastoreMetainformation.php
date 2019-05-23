<?php

namespace webfilesframework\core\datastore\types\directory;

use webfilesframework\core\datasystem\file\format\MWebfile;

class MDirectoryDatastoreMetainformation extends MWebfile
{
    /** @var string */
    private $m_sTimezone;

    /** @var bool */
    private $m_bIsNormalized;
    /** @var bool */
    private $m_bUseHumanReadableTimestamps;
    /** @var bool */
    private $m_bContainsThumbnails;


    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->m_sTimezone;
    }

    /**
     * @param string $m_sTimezone
     */
    public function setTimezone($m_sTimezone)
    {
        $this->m_sTimezone = $m_sTimezone;
    }

    /**
     * @return bool
     */
    public function isNormalized()
    {
        return $this->m_bIsNormalized;
    }

    /**
     * @param bool $m_bIsNormalized
     */
    public function setNormalized($m_bIsNormalized)
    {
        $this->m_bIsNormalized = $m_bIsNormalized;
    }

    /**
     * @return bool
     */
    public function isUseHumanReadableTimestamps()
    {
        return $this->m_bUseHumanReadableTimestamps;
    }

    /**
     * @param bool $useHumanReadableTimestamps
     */
    public function setUseHumanReadableTimestamps($useHumanReadableTimestamps)
    {
        $this->m_bUseHumanReadableTimestamps = $useHumanReadableTimestamps;
    }

    /**
     * @return bool
     */
    public function containsThumbnails()
    {
        return $this->m_bContainsThumbnails;
    }

    /**
     * @param bool $m_bCreatedThumbnails
     */
    public function setContainsThumbnails($m_bCreatedThumbnails)
    {
        $this->m_bContainsThumbnails = $m_bCreatedThumbnails;
    }


}