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

    private $m_sDatastoreUrl;

    public static $m__sClassName = __CLASS__;

    public function __construct($datastoreUrl)
    {
        $this->m_sDatastoreUrl = $datastoreUrl;
    }

    public function tryConnect()
    {
        // TODO
        return true;
    }

    public function isReadOnly()
    {
        // TODO
        return false;
    }

    private function doRemoteCall($data = null)
    {
        $request = new MPostHttpRequest($this->m_sDatastoreUrl, $data);
        $response = $request->makeRequest();

        return $response;
    }

    public function getWebfilesAsStream($data = null)
    {

        $callResult = $this->doRemoteCall($data);
        return new MWebfileStream($callResult);
    }

    public function getWebfilesAsArray()
    {
        return $this->getWebfilesAsStream()->getWebfiles();
    }

    public function searchByTemplate(MWebfile $template)
    {

        $data = array();
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD] = MRemoteDatastoreEndpoint::$METHOD_NAME_SEARCH_BY_TEMPLATE;
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_TEMPLATE] = $template->marshall();

        return $this->getWebfilesAsStream($data)->getWebfiles();
    }

    public function getLatestWebfiles($count = 5)
    {
        // TODO
    }

    public function getNextWebfileForTimestamp($time)
    {
        // TODO: Implement getNextWebfileForTimestamp() method.
        return null;
    }


    public function storeWebfile(MWebfile $webfile)
    {

        $data = array();
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD] = MRemoteDatastoreEndpoint::$METHOD_NAME_STORE_WEBFILE;
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_WEBFILE] = $webfile->marshall();

        $this->doRemoteCall($data);
    }

    public function deleteByTemplate(MWebfile $template)
    {
        $data = array();
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD] = MRemoteDatastoreEndpoint::$METHOD_NAME_DELETE_BY_TEMPLATE;
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_TEMPLATE] = $template->marshall();

        $this->doRemoteCall($data);
    }

}