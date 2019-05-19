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
            <p>You are connected to a webfile remote datastore but you did not pass any parameter.</p>
            <p>Remote datastores supports get and post method of http protocol. To query the datastore you have to to pass the following parameters:</p>
            <table>
                <tr>
                    <th>Parameters</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>method: retrieveWebfiles, </td>
                    <td></td>
                </tr>
                <tr>
                    <td>method: searchByTemplate, template: </td>
                    <td></td>
                </tr>
                <tr>
                    <td>method: storeWebfile</td>
                    <td></td>
                </tr>
                <tr>
                    <td>method: deleteByTemplate</td>
                    <td></td>
                </tr>
            </table>
            <b>Hint:</b> The class MRemoteDatastore helps you to connect to an MRemoteDatastoreEndpoint.
			<?php
		}

	}

}