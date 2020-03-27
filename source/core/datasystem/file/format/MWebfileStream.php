<?php

namespace webfilesframework\core\datasystem\file\format;

use webfilesframework\MWebfilesFrameworkException;

/**
 * Defines the representation of a list of webfiles.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWebfileStream
{

    private $webfiles;

	/**
	 * MWebfileStream constructor.
	 *
	 * @param $input
	 *
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
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
	 * @param $webfiles
	 *
	 * @throws MWebfilesFrameworkException
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

	/**
	 * @param $input
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	private function unmarshall($input)
    {

        $webfilesResultArray = array();

        $root = $this->parseAndValidateWebfilesStreamXml($input);
        $webfilesChildren = $root->webfiles->children();

        /** @var \SimpleXMLElement $webfileChild */
        foreach ($webfilesChildren as $webfileChild) {
            array_push(
                $webfilesResultArray, MWebfile::staticUnmarshall($webfileChild->asXML()));
        }

        return $webfilesResultArray;
    }

    /**
     * @param string $input
     * @return \SimpleXMLElement
     * @throws MWebfilesFrameworkException
     */
    private function parseAndValidateWebfilesStreamXml($input) {

        $root = @simplexml_load_string($input);

        if ( $root == null ) {
            throw new MWebfilesFrameworkException(
                "Error on reading xml of webfile stream: No root element given. Input: " . $input);
        }

        if ( $root === false ) {
            throw new MWebfilesFrameworkException(
                "Error on reading xml of webfile stream: Input: " . $input);
        }

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

        return $root;
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
