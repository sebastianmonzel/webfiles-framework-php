<?php

namespace simpleserv\webfilesframework\core\datastore\types\remote;

use webfilesframework\core\datastore\MAbstractDatastore;
use webfilesframework\core\datastore\webfilestream\MWebfileStream;
use webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Class MRemoteDatastoreEndpoint
 * @package simpleserv\webfilesframework\core\datastore\types\remote
 */
class MRemoteDatastoreEndpoint {
	/** @var MAbstractDatastore */
	private $m_oDatastore;

	public static $METHOD_NAME_SEARCH_BY_TEMPLATE = "searchByTemplate";
	public static $METHOD_NAME_STORE_WEBFILE = "storeWebfile";
	public static $METHOD_NAME_DELETE_BY_TEMPLATE = "deleteByTemplate";

	public static $PAYLOAD_FIELD_NAME_WEBFILE = "webfile";
	public static $PAYLOAD_FIELD_NAME_TEMPLATE = "template";
	public static $PAYLOAD_FIELD_NAME_METHOD = "method";


	public function __construct( MAbstractDatastore $datastore ) {
		$this->m_oDatastore = $datastore;
	}

	public function issetParam( $name ) {
		return isset( $_GET[ $name ] ) || isset( $_POST[ $name ] );
	}

	public function getParam( $name ) {
		if ( isset( $_GET[ $name ] ) ) {
			return $_GET[ $name ];
		} else {
			return $_POST[ $name ];
		}
	}

	/**
	 * @throws \ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 */
	public function handleRemoteCall() {

		if ( $this->issetParam( static::$PAYLOAD_FIELD_NAME_METHOD ) ) {

			if (
				$this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_SEARCH_BY_TEMPLATE
				&& $this->issetParam( static::$PAYLOAD_FIELD_NAME_TEMPLATE ) ) {

				// GET BY TEMPLATE
				$template = MWebfile::staticUnmarshall( $_POST[ static::$PAYLOAD_FIELD_NAME_TEMPLATE ] );

				$webfiles       = $this->m_oDatastore->searchByTemplate( $template );
				$webfilesStream = new MWebfileStream( $webfiles );

			} else if (
				$this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_STORE_WEBFILE
				&& $this->issetParam( static::$PAYLOAD_FIELD_NAME_WEBFILE ) ) {

				// STORE
				$webfile = MWebfile::staticUnmarshall( $_POST[ static::$PAYLOAD_FIELD_NAME_WEBFILE ] );
				$this->m_oDatastore->storeWebfile( $webfile );

			} else if (
				$this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_DELETE_BY_TEMPLATE
				&& $this->issetParam( static::$PAYLOAD_FIELD_NAME_TEMPLATE ) ) {

				// DELETE
				$webfile = MWebfile::staticUnmarshall( getParam(static::$PAYLOAD_FIELD_NAME_TEMPLATE) );
				$this->m_oDatastore->deleteByTemplate( $webfile );

			} else {
				$webfilesStream = $this->m_oDatastore->getWebfilesAsStream();
			}

		}

		if ( isset( $webfilesStream ) ) {
			echo $webfilesStream->getXML();
		} else {
			?>
            <h1>Remote datastore</h1>
            <p>Success: You are connected to a webfile remote datastore but you did not pass any parameter or not the right ones.</p>
            <p>Remote datastores supports parameter submission via get and via post method of http protocol. To query the datastore you have to to pass the following parameters:</p>
            <table border="1">
                <tr>
                    <th>Parameters</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td><b>method</b>: <a href="?method=retrieveWebfiles">retrieveWebfiles</a></td>
                    <td>retrieves all templates.</td>
                </tr>
                <tr>
                    <td><b>method</b>: searchByTemplate, <b>template</b>: template with criteria to search for</td>
                    <td>retrieves the webfiles matching the template criteria.</td>
                </tr>
                <tr>
                    <td><b>method</b>: "storeWebfile", <b>webfile</b>: the webfile to be stored.</td>
                    <td>stores the submitted </td>
                </tr>
                <tr>
                    <td><b>method</b>: "deleteByTemplate", <b>template</b>: template with criteria to delete</td>
                    <td>deletes the webfiles matching the template criteria.</td>
                </tr>
            </table>
            <p>
                <b>Hint:</b> The class MRemoteDatastore helps you to connect to an MRemoteDatastoreEndpoint like this.
            </p>
			<?php
		}

	}

}