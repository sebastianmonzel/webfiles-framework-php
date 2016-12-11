<?php

namespace simpleserv\webfilesframework\core\datastore\types\remote;


use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;
use simpleserv\webfilesframework\io\request\MPostHttpRequest;
use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Encapsulates the access to a datastore with help of the content
 * information service.
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MRemoteDatastore extends MAbstractDatastore
{

    private $m_sDatastoreName;
    private $m_sWebfilesUrl;

    public static $m__sClassName = __CLASS__;

    public function __construct($contentInfoServiceUrl, $datastoreId)
    {
        $this->m_sWebfilesUrl = $contentInfoServiceUrl;
        $this->m_sDatastoreName = $datastoreId;
    }

    public function tryConnect()
    {
        return true;
    }

    public function isReadOnly()
    {
        return true;
    }

    public function getWebfilesAsStream($data = null)
    {

        $requestResult = $this->makeRequest($data);
        return new MWebfileStream($requestResult);
    }


    private function makeRequest($data = null)
    {

        $requestUrl = $this->m_sWebfilesUrl . "/services/contentInformationService/?datastoreId=" . $this->m_sDatastoreName;
        $request = new MPostHttpRequest($requestUrl, $data);
        $webfilestreamContent = $request->makeRequest();

        return $webfilestreamContent;
    }

    public function getWebfilesAsArray()
    {
        return $this->getWebfilesAsStream()->getWebfiles();
    }

    public function getLatestWebfiles($count = 5)
    {
        // TODO
    }

    public function searchByTemplate(MWebfile $template)
    {

        $data = array();
        $data['method'] = "getByTemplate";
        $data['template'] = $template->marshall();

        return $this->getWebfilesAsStream($data)->getWebfiles();
    }

    /**
     * Returns the next webfile for the given timestamp
     * @param int $time timestamp in unix-format.
     * @return MWebfile webfile according to the given input.
     * old: getNextWebfileForTime - new getNextWebfileForTimestamp
     * DONE
     */
    public function getNextWebfileForTimestamp($time)
    {
        // TODO: Implement getNextWebfileForTimestamp() method.
        return null;
    }
}