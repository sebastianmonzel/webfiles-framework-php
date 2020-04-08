<?php

namespace webfilesframework\core\datastore\types\remote;


use ReflectionException;
use webfilesframework\core\datastore\MAbstractDatastore;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;
use webfilesframework\io\request\MPostHttpRequest;
use webfilesframework\MWebfilesFrameworkException;

/**
 * Encapsulates the access to a datastore with help of the content
 * information service.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MRemoteDatastore extends MAbstractDatastore
{
	// TODO exceptions von originärem datastore wie druchreichen?
    private $m_sDatastoreUrl;


    public function __construct($datastoreUrl)
    {
        $this->m_sDatastoreUrl = $datastoreUrl;
    }

    public function tryConnect()
    {
	    $data = array();
	    $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD] = MRemoteDatastoreEndpoint::$METHOD_NAME_TRY_CONNECT;

	    $isReadOnly = $this->doRemoteCall($data);

	    if ( $isReadOnly == "true" ) {
		    return true;
	    } else {
		    return false;
	    }
    }

    public function isReadOnly()
    {
	    $data = array();
	    $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD] = MRemoteDatastoreEndpoint::$METHOD_NAME_IS_READ_ONLY;

	    $isReadOnly = $this->doRemoteCall($data);

	    if ( $isReadOnly == "true" ) {
	    	return true;
	    } else {
	    	return false;
	    }

    }

	/**
	 * @param null $data
	 *
	 * @return MWebfileStream
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 */
    public function getAllWebfiles($data = null)
    {
        $callResult = $this->doRemoteCall($data);
        return new MWebfileStream($callResult);
    }

	/**
	 * @param MWebfile $template
	 *
	 * @return MWebfileStream
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public function searchByTemplate(MWebfile $template)
    {

        $data = array();
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD]   = MRemoteDatastoreEndpoint::$METHOD_NAME_SEARCH_BY_TEMPLATE;
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_TEMPLATE] = $template->marshall();

        return $this->getAllWebfiles($data);
    }

	/**
	 * @param int $count
	 *
	 * @return MWebfileStream
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public function getLatestWebfiles($count = 5)
    {
	    $data = array();
	    $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD] = MRemoteDatastoreEndpoint::$METHOD_NAME_GET_LATEST_WEBFILES;
	    $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_COUNT]  = $count;

	    return $this->getAllWebfiles($data);
    }

	/**
	 * @param $timestamp
	 */
    public function getNextWebfileForTimestamp($timestamp)
    {
	    $data = array();
	    $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD] = MRemoteDatastoreEndpoint::$METHOD_NAME_GET_NEXT_WEBFILE_FOR_TIMESTAMP;
	    $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_TIMESTAMP] = $timestamp;

	    $this->doRemoteCall($data);
    }

	/**
	 * @param MWebfile $webfile
	 *
	 * @return MWebfileStream
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 */
    public function storeWebfile(MWebfile $webfile)
    {

        $data = array();
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD]  = MRemoteDatastoreEndpoint::$METHOD_NAME_STORE_WEBFILE;
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_WEBFILE] = $webfile->marshall();

	    $callResultAsWebfileStreamXml = $this->doRemoteCall($data);

	    return new MWebfileStream($callResultAsWebfileStreamXml);
    }

	/**
	 * @param MWebfile $template
	 *
	 * @return MWebfileStream
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 */
    public function deleteByTemplate(MWebfile $template)
    {
        $data = array();
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD]   = MRemoteDatastoreEndpoint::$METHOD_NAME_DELETE_BY_TEMPLATE;
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_TEMPLATE] = $template->marshall();

	    $callResultAsWebfileStreamXml = $this->doRemoteCall($data);

	    return new MWebfileStream($callResultAsWebfileStreamXml);
    }

	private function doRemoteCall($data = null)
	{
		$request = new MPostHttpRequest($this->m_sDatastoreUrl, $data);
		$response = $request->makeRequest();

		return $response;
	}

}