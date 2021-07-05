<?php

namespace webfilesframework\core\datatype\webfiledefinition;

/**
 * Class MTextMessageWebfile
 * @package webfilesframework\core\datastore\types\database
 */
class MTextMessageWebfile extends MWebfile
{

    private $m_sText;

    /**
     * @param mixed $m_sText
     */
    public function setText($m_sText)
    {
        $this->m_sText = $m_sText;
    }

    /**
     * @return mixed
     */
    public function getText() {
        return $this->m_sText;
    }

}