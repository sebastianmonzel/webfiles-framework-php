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

    private $m_sDatastoreUrl;


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
	    $data = array();
	    $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD] = MRemoteDatastoreEndpoint::$METHOD_NAME_IS_READ_ONLY;

	    $this->doRemoteCall($data);
    }

	/**
	 * @param null $data
	 *
	 * @return MWebfileStream
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 */
    public function getWebfilesAsStream($data = null)
    {
        $callResult = $this->doRemoteCall($data);
        return new MWebfileStream($callResult);
    }

	/**
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public function getWebfilesAsArray()
    {
        return $this->getWebfilesAsStream()->getWebfiles();
    }

	/**
	 * @param MWebfile $template
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public function searchByTemplate(MWebfile $template)
    {

        $data = array();
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD]   = MRemoteDatastoreEndpoint::$METHOD_NAME_SEARCH_BY_TEMPLATE;
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_TEMPLATE] = $template->marshall();

        return $this->getWebfilesAsStream($data)->getWebfiles();
    }

	/**
	 * @param int $count
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public function getLatestWebfiles($count = 5)
    {
	    $data = array();
	    $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD] = MRemoteDatastoreEndpoint::$METHOD_NAME_SEARCH_BY_TEMPLATE;
	    $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_COUNT]  = $count;

	    return $this->getWebfilesAsStream($data)->getWebfiles();
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
	 * @throws ReflectionException
	 */
    public function storeWebfile(MWebfile $webfile)
    {

        $data = array();
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD]  = MRemoteDatastoreEndpoint::$METHOD_NAME_STORE_WEBFILE;
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_WEBFILE] = $webfile->marshall();

        $this->doRemoteCall($data);
    }

	/**
	 * @param MWebfile $template
	 *
	 * @throws ReflectionException
	 */
    public function deleteByTemplate(MWebfile $template)
    {
        $data = array();
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_METHOD]   = MRemoteDatastoreEndpoint::$METHOD_NAME_DELETE_BY_TEMPLATE;
        $data[MRemoteDatastoreEndpoint::$PAYLOAD_FIELD_NAME_TEMPLATE] = $template->marshall();

        $this->doRemoteCall($data);
    }

	private function doRemoteCall($data = null)
	{
		$request = new MPostHttpRequest($this->m_sDatastoreUrl, $data);
		$response = $request->makeRequest();

		return $response;
	}

}