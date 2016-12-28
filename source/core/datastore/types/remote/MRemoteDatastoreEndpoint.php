<?php

namespace simpleserv\webfilesframework\core\datastore\types\remote;

use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;
use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Class MRemoteDatastoreEndpoint
 * @package simpleserv\webfilesframework\core\datastore\types\remote
 */
class MRemoteDatastoreEndpoint
{
    /** @var MAbstractDatastore  */
    private $m_oDatastore;

    public static $METHOD_NAME_SEARCH_BY_TEMPLATE = "searchByTemplate";
    public static $METHOD_NAME_STORE_WEBFILE = "storeWebfile";

    public static $PAYLOAD_FIELD_NAME_WEBFILE = "webfile";
    public static $PAYLOAD_FIELD_NAME_TEMPLATE = "template";
    public static $PAYLOAD_FIELD_NAME_METHOD = "method";


    public function __construct(MAbstractDatastore $datastore) {
        $this->m_oDatastore = $datastore;
    }

    public function handleRemoteCall() {

        if ( isset($_POST[static::PAYLOAD_FIELD_NAME_METHOD])) {

            if (
                $_POST[static::PAYLOAD_FIELD_NAME_METHOD] == static::$METHOD_NAME_SEARCH_BY_TEMPLATE
                    && isset($_POST[static::PAYLOAD_FIELD_NAME_TEMPLATE]) ) {

                // GET BY TEMPLATE
                $template = MWebfile::staticUnmarshall($_POST[static::$PAYLOAD_FIELD_NAME_TEMPLATE]);

                $webfiles = $this->m_oDatastore->searchByTemplate($template);
                $webfilesStream = new MWebfileStream($webfiles);

            } else if (
                $_POST[static::PAYLOAD_FIELD_NAME_METHOD] == static::$METHOD_NAME_STORE_WEBFILE
                    && isset($_POST[static::PAYLOAD_FIELD_NAME_WEBFILE])) {

                // STORE
                $webfile = MWebfile::staticUnmarshall($_POST[static::PAYLOAD_FIELD_NAME_WEBFILE]);
                $this->m_oDatastore->storeWebfile($webfile);

            } else {
                $webfilesStream = $this->m_oDatastore->getWebfilesAsStream();
            }

        } else {
            $webfilesStream = $this->m_oDatastore->getWebfilesAsStream();
        }
        if ( isset($webfilesStream) ) {
            echo $webfilesStream->getXML();
        }

    }

}