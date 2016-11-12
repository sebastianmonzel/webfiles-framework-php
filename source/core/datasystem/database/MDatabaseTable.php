<?php

namespace simpleserv\webfilesframework\core\datasystem\database;

use simpleserv\webfilesframework\MItem;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseTableColumn;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatabaseTable
{

    var $name = null;

    var $primaryKey = null;
    var $identifier = null;
    var $identifierSize = null;

    var $columns = array();

    var $databaseConnection;

    /**
     *
     * Enter description here ...
     * @param unknown_type $name
     */
    public function __construct(MDatabaseConnection $databaseConnection, $name)
    {

        $this->databaseConnection = $databaseConnection;
        $this->name = $name;

        /*$this->setIdentifier("id", "10");
        $this->addColumn("title",MIDbDatatypes::varchar(), 250);
        $this->addColumn("content",MIDbDatatypes::text());
        $this->addColumn("bla", MIDbDatatypes::int(),30);
        $this->addColumn("postActionId",MIDbDatatypes::int(), 20);*/

    }


    /**
     *
     * Enter description here ...
     */
    public function create()
    {
        $query = "CREATE TABLE IF NOT EXISTS `" . $this->name . "` (";

        if ($this->identifier != null && $this->identifierSize != null) {
            $query .= "`" . $this->identifier . "` int(" . $this->identifierSize . ") NOT NULL AUTO_INCREMENT,";
        }
        foreach ($this->columns as $value) {
            $query .= $value->getStringRepresentation();
        }

        if ($this->primaryKey != null) {
            $query .= "PRIMARY KEY (`" . $this->primaryKey . "`)";
        }
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $this->databaseConnection->query($query);
    }


    /**
     *
     * Enter description here ...
     */
    public function drop()
    {
        $this->databaseConnection->query(
            "DROP TABLE IF EXISTS `" . $this->name . "`"
        );
    }


    /**
     *
     * Enter description here ...
     * @param unknown_type $columnName
     * @param unknown_type $size
     */
    public function setIdentifier($columnName, $size)
    {
        $this->identifier = $columnName;
        $this->identifierSize = $size;
        $this->setPrimaryKey($columnName);
    }


    /**
     *
     * Enter description here ...
     * @param string $columnName
     */
    public function setPrimaryKey($columnName)
    {
        $this->primaryKey = $columnName;
    }


    /**
     *
     * Enter description here ...
     * @param string $name
     * @param string $type
     * @param int $length
     */
    public function addColumn($name, $type, $length = null)
    {
        $column = new MDatabaseTableColumn($name, $type, $length);
        array_push($this->columns, $column);
    }

}