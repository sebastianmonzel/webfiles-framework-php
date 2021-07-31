<?php

namespace webfilesframework\core\datasystem\file\format;

use ReflectionException;
use SimpleXMLElement;
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
	 * @throws ReflectionException
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

    private function marshallAsXML()
    {

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";

        $xml .= "<webfilestream><webfiles>";
        /** @var MWebfile $webfile */
        foreach ($this->webfiles as $webfile) {
            $xml .= $webfile->marshall(false, false);
        }
        $xml .= "</webfiles></webfilestream>";
        return $xml;
    }

    private function marshallAsJSON()
    {

        $json = "[\n";
        /** @var MWebfile $webfile */
        foreach ($this->webfiles as $webfile) {
            $json .= $webfile->marshall(false, true);
            if (next($this->webfiles)==true) $json .= "\n,\n";
        }
        $json .= "]";
        return $json;
    }

	/**
	 * @param $input
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
	private function unmarshall($input)
    {
        if ( substr(trim($input),0,1) == "[" ) {
            return $this->unmarshallAsJson($input);
        } else {
            return $this->unmarshallAsXml($input);
        }
    }

    /**
     * @param string $input
     * @return SimpleXMLElement
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

        /** @var SimpleXMLElement $rootChild */
        foreach ($rootChildren as $rootChild) {
            if ( $rootChild->getName() != "webfiles" ) {
                throw new MWebfilesFrameworkException("No webfiles child exists on root element. Input: " . $input);
            }
        }

        return $root;
    }

    /**
     * @param string $input
     * @return SimpleXMLElement
     * @throws MWebfilesFrameworkException
     */
    private function parseAndValidateWebfilesStreamJson($jsonAsString) {

        $jsonRoot = json_decode($jsonAsString, true);

        if ($jsonRoot == null) {
            throw new MWebfilesFrameworkException("Error on reading initial json: " . $jsonAsString);
        }

        return $jsonRoot;
    }


    public function getXML()
    {
        return $this->marshallAsXML();
    }

    public function getJSON()
    {
        return $this->marshallAsJSON();
    }

    public function getArray()
    {
        return $this->webfiles;
    }

    /**
     * @param $input
     * @return array
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    private function unmarshallAsXml($input): array
    {
        $webfilesResultArray = array();

        $root = $this->parseAndValidateWebfilesStreamXml($input);
        $webfilesChildren = $root->webfiles->children();

        /** @var SimpleXMLElement $webfileChild */
        foreach ($webfilesChildren as $webfileChild) {
            array_push(
                $webfilesResultArray, MWebfile::staticUnmarshall($webfileChild->asXML()));
        }

        return $webfilesResultArray;
    }

    /**
     * @param $input
     * @return array
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    private function unmarshallAsJson($input): array
    {
        $webfilesResultArray = array();

        $root = $this->parseAndValidateWebfilesStreamJson($input);

        /** @var SimpleXMLElement $webfileChild */
        foreach ($root as $webfileChild) {
            array_push(
                $webfilesResultArray, MWebfile::staticUnmarshall($webfileChild));
        }

        return $webfilesResultArray;
    }
}
