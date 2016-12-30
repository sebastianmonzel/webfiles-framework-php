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
            $this->validateWebfilesArray($input);
            $this->webfiles = $input;
        } else if (is_string($input)) {
            $this->webfiles = $this->unmarshall($input);
        } else if ($input instanceof MWebfile) {
            $this->webfiles = array();
            array_push($this->webfiles, $input);
        } else if (isset($input)) {
            throw new MWebfilesFrameworkException("Cannot handle input for creating webfile stream. input: " . $input);
        }
    }

    /**
     * @param array $webfiles
     */
    private function validateWebfilesArray($webfiles) {

        foreach ($webfiles as $webfile) {
            if ( ! $webfile instanceof MWebfile) {
                throw new MWebfilesFrameworkException("Not all elements in array are from type MWebfile.");
            }
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

        $webfilesResultArray = array();

        $root = @simplexml_load_string($input);

        if ($root != null && $root != false) {

            $rootChildren = $root->children();

            if ( count($rootChildren) != 1 ) {
                throw new MWebfilesFrameworkException("Root element has not exactly one child. Input: " . $input);
            }

            /** @var \SimpleXMLElement $rootChild */
            foreach ($rootChildren as $rootChild) {
                if ( $rootChild->getName() != "webfiles" ) {
                    throw new MWebfilesFrameworkException("No webfiles child exists on root element. Input: " . $input);
                }
            }

            $webfilesChildren = $root->webfiles->children();

            /** @var \SimpleXMLElement $webfileChild */
            foreach ($webfilesChildren as $webfileChild) {
                array_push(
                    $webfilesResultArray, MWebfile::staticUnmarshall($webfileChild->asXML()));
            }

        } else {
            throw new MWebfilesFrameworkException(
                "Error on reading xml of webfile stream: No root element given. Input: " . $input);
        }

        return $webfilesResultArray;
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
