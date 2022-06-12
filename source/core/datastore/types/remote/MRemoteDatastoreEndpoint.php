<?php

namespace webfilesframework\core\datastore\types\remote;


use ReflectionException;
use webfilesframework\core\datastore\MAbstractDatastore;
use webfilesframework\core\datastore\MDatastoreException;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;
use webfilesframework\MWebfilesFrameworkException;

/**
 * Class MRemoteDatastoreEndpoint
 * @package webfilesframework\core\datastore\types\remote
 */
class MRemoteDatastoreEndpoint {
	/** @var MAbstractDatastore */
	private $m_oDatastore;
	private $m_bOverwriteReadOnlyWithTrue;

	// READ
	public static $METHOD_NAME_IS_READ_ONLY                   = "isReadOnly";
	public static $METHOD_NAME_TRY_CONNECT                    = "tryConnect";
	public static $METHOD_NAME_RETRIEVE_WEBFILES              = "retrieveWebfiles";
	public static $METHOD_NAME_SEARCH_BY_TEMPLATE             = "searchByTemplate";
	public static $METHOD_NAME_GET_LATEST_WEBFILES            = "getLatestWebfiles";
	public static $METHOD_NAME_GET_NEXT_WEBFILE_FOR_TIMESTAMP = "getNextWebfileForTimestamp";

	// WRITE
	public static $METHOD_NAME_STORE_WEBFILE = "storeWebfile";
	public static $METHOD_NAME_DELETE_BY_TEMPLATE = "deleteByTemplate";

	// PAYLOAD
	public static $PAYLOAD_FIELD_NAME_WEBFILE     = "webfile";
	public static $PAYLOAD_FIELD_NAME_TEMPLATE    = "template";
	public static $PAYLOAD_FIELD_NAME_METHOD      = "method";
	public static $PAYLOAD_FIELD_NAME_COUNT       = "count";
	public static $PAYLOAD_FIELD_NAME_TIMESTAMP   = "timestamp";

	public static $TYPE_XML                      = "xml";


	public function __construct( MAbstractDatastore $datastore,  bool $overwriteReadOnlyWithTrue = false ) {
		$this->m_oDatastore = $datastore;
		$this->m_bOverwriteReadOnlyWithTrue = $overwriteReadOnlyWithTrue;
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
	 * Will be called in an endpoint to handle incoming calls to the remote datastore.
	 *
	 * @return void;
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 * @throws MDatastoreException
	 */
	public function handleRemoteCall() {

        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Origin: *");

        if ( $this->issetParam( static::$PAYLOAD_FIELD_NAME_METHOD ) ) {

			if ( $this->isRetrieveWebfiles() ) { // GET BY TEMPLATE

				$webfilesStream = $this->m_oDatastore->getAllWebfiles();
                $this->writeOutWebfilesStream($webfilesStream);

                return;

			} else if ( $this->isSearchByTemplate() ) { // GET BY TEMPLATE

				$template = MWebfile::staticUnmarshall( $_POST[ static::$PAYLOAD_FIELD_NAME_TEMPLATE ] );

				$webfilesStream = $this->m_oDatastore->searchByTemplate($template);
                $this->writeOutWebfilesStream($webfilesStream);

				return;

			} else if ( $this->isStoreWebfile() ) {    // STORE WEBFILE

				$webfile = MWebfile::staticUnmarshall( $_POST[ static::$PAYLOAD_FIELD_NAME_WEBFILE ] );
				$this->m_oDatastore->storeWebfile($webfile);

				$webfilesStream = $this->m_oDatastore->getAllWebfiles();
                $this->writeOutWebfilesStream($webfilesStream);

				return;

			} else if ( $this->isGetLatestWebfiles() ) { // GET LATEST WEBFILES

				$count = $_POST[static::$PAYLOAD_FIELD_NAME_COUNT];
				$webfilesStream = $this->m_oDatastore->getLatestWebfiles($count);
                $this->writeOutWebfilesStream($webfilesStream);

				return;

			} else if ( $this->isGetNextWebfileForTimestamp() ) { // GET NEXT WEBFILE FOR TIMESTAMP

				$next_webfile_for_timestamp = $this->m_oDatastore->getNextWebfileForTimestamp( $_POST[ static::$PAYLOAD_FIELD_NAME_TIMESTAMP ] );
				echo $next_webfile_for_timestamp->marshall();

				return;

			} else if ( $this->isDeleteByTemplate() ) { // DELETE BY TEMPLATE

				$webfile = MWebfile::staticUnmarshall( $this->getParam( static::$PAYLOAD_FIELD_NAME_TEMPLATE ) );
				$this->m_oDatastore->deleteByTemplate( $webfile );

				$webfilesStream = $this->m_oDatastore->getAllWebfiles();
                $this->writeOutWebfilesStream($webfilesStream);

				return;

			} else if ( $this->isReadOnly() ) { // IS READONLY

				$isReadOnly = $this->m_oDatastore->isReadOnly();
				if ( $isReadOnly || $this->m_bOverwriteReadOnlyWithTrue) {
				    echo "true";
                } else {
				    echo "false";
                }

				return;

			} else if ( $this->isTryConnect() ) { // IS TRYCONNECT

				$tryConnect = $this->m_oDatastore->tryConnect();
				if ( $tryConnect ) {
					echo "true";
				} else {
					echo "false";
				}

				return;

			}

		} else {

			$webfilesStream = $this->m_oDatastore->getAllWebfiles();
            $this->writeOutWebfilesStream($webfilesStream);

			return;
		}
		// Write documentation if no case matches: TODO nonsense since "else" always skips this block
		?>
        <h1>Remote datastore</h1>
        <p>Success: You are connected to a webfile remote datastore but you did not pass any parameter or not the right
            ones.</p>
        <p>Remote datastores supports parameter submission via get and via post method of http protocol. To query the
            datastore you have to to pass the following parameters:</p>
        <table style="border-width: 1px">
            <tr>
                <th>Parameters</th>
                <th>Description</th>
            </tr>
            <tr>
                <td><b>method</b>: <a href="?method=retrieveWebfiles">retrieveWebfiles</a></td>
                <td>retrieves all webfiles.</td>
            </tr>
            <tr>
                <td><b>method</b>: searchByTemplate, <b>template</b>: template with criteria to search for</td>
                <td>retrieves the webfiles matching the template criteria.</td>
            </tr>
            <tr>
                <td><b>method</b>: "storeWebfile", <b>webfile</b>: the webfile to be stored.</td>
                <td>stores the submitted webfile definition</td>
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

	/**
	 * @return bool
	 */
	public function isDeleteByTemplate(): bool {
		return $this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_DELETE_BY_TEMPLATE
		       && $this->issetParam( static::$PAYLOAD_FIELD_NAME_TEMPLATE );
	}

	/**
	 * @return bool
	 */
	public function isGetNextWebfileForTimestamp(): bool {
		return $this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_GET_NEXT_WEBFILE_FOR_TIMESTAMP
		       && $this->issetParam( static::$PAYLOAD_FIELD_NAME_TIMESTAMP );
	}

	/**
	 * @return bool
	 */
	public function isGetLatestWebfiles(): bool {
		return $this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_GET_LATEST_WEBFILES
		       && $this->issetParam( static::$PAYLOAD_FIELD_NAME_COUNT );
	}

	/**
	 * @return bool
	 */
	public function isStoreWebfile(): bool {
		return $this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_STORE_WEBFILE
		       && $this->issetParam( static::$PAYLOAD_FIELD_NAME_WEBFILE );
	}

	/**
	 * @return bool
	 */
	public function isSearchByTemplate(): bool {
		return $this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_SEARCH_BY_TEMPLATE
		       && $this->issetParam( static::$PAYLOAD_FIELD_NAME_TEMPLATE );
	}

	/**
	 * @return bool
	 */
	public function isRetrieveWebfiles(): bool {
		return $this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_RETRIEVE_WEBFILES;
	}

	/**
	 * @return bool
	 */
	public function isReadOnly(): bool {
		return $this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_IS_READ_ONLY;
	}

	/**
	 * @return bool
	 */
	public function isTryConnect(): bool {
		return $this->getParam( static::$PAYLOAD_FIELD_NAME_METHOD ) == static::$METHOD_NAME_TRY_CONNECT;
	}

    /**
     * @param MWebfileStream $webfilesStream
     */
    private function writeOutWebfilesStream(MWebfileStream $webfilesStream): void
    {
        if ($this->issetParam(static::$TYPE_XML)) {
            header('Content-Type: application/xml');
            echo $webfilesStream->getXML();
        } else {
            header('Content-Type: application/json');
            echo $webfilesStream->getJSON();
        }
    }

}