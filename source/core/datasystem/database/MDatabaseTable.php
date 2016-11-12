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
     * Spe
     * @param string $columnName
     * @param string $size
     */
    public function specifyIdentifier($columnName, $size)
    {
        $this->identifier = $columnName;
        $this->identifierSize = $size;
        $this->setPrimaryKey($columnName);
    }


    /**
     * Sets the primary key of the table.
     * @param string $columnName
     */
    public function setPrimaryKey($columnName)
    {
        $this->primaryKey = $columnName;
    }


    /**
     * Adds a new column to the actual database representation.
     *
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