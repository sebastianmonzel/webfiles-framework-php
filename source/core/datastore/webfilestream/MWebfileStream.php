<?php

namespace simpleserv\webfilesframework\core\datastore\webfilestream;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
use simpleserv\webfilesframework\MWebfilesFrameworkException;

/**
 * Defines the representation of a list of webfiles.
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWebfileStream
{

    private $webfiles;

    public function __construct($input)
    {

        if (is_array($input)) {
            $this->webfiles = $input;
        } else if (is_string($input)) {
            $this->webfiles = $this->unmarshall($input);
        } else if ($input instanceof MWebfile) {
            $this->webfiles = array();
            array_push($this->webfiles, $input);
        } else if (isset($input)) {
            throw new MWebfilesFrameworkException("Cannot handle input for creating webfile stream.");
        }
    }

    private function marshall()
    {

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";

        $xml .= "<webfilestream><webfiles>";
        foreach ($this->webfiles as $webfile) {
            $xml .= $webfile->marshall(false);
        }
        $xml .= "</webfiles></webfilestream>";
        return $xml;
    }

    private function unmarshall($input)
    {

        $webfiles = array();

        $root = simplexml_load_string($input);

        if ($root != null) {

            $webfilesChildren = $root->webfiles->children();

            /** @var \SimpleXMLElement $webfileChild */
            foreach ($webfilesChildren as $webfileChild) {
                array_push(
                    $webfiles,
                    MWebfile::staticUnmarshall($webfileChild->asXML()));
            }

        } else {
            throw new MWebfilesFrameworkException(
                "Error on reading xml of webfile stream: No root element given.");
        }

        return $webfiles;
    }

    public function getXML()
    {
        return $this->marshall();
    }

    public function getWebfiles()
    {
        return $this->webfiles;
    }
}
