<?php

namespace simpleserv\webfilesframework\core\datastore\types\database;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseTable;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseDatatypes;
use simpleserv\webfilesframework\core\datastore\MISingleDatastore;

use simpleserv\webfilesframework\core\datastore\functions\sorting\MAscendingSorting;
use simpleserv\webfilesframework\core\datastore\functions\sorting\MDescendingSorting;
use simpleserv\webfilesframework\core\time\MTimespan;

/**
 * Class to connect to a datastore based on a database.
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatabaseDatastore extends MAbstractDatastore
    implements MISingleDatastore
{

    private $databaseConnection;

    public function __construct(MDatabaseConnection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    public function tryConnect()
    {
        return $this->databaseConnection->connect();
    }

    public function isReadOnly()
    {
        return false;
    }

    public function getNextWebfileForTimestamp($time)
    {

    }

    public function getTime()
    {
        return NULL;
    }

    public function getGeograficPosition()
    {
        return NULL;
    }

    /**
     * Creates a database table to persist objects of this type.
     * @param MWebfile $webfile
     * @param boolean $dropTableIfExists
     */
    private function createTable(MWebfile $webfile, $dropTableIfExists = true)
    {

        $tableName = $this->getDatabaseTableName($webfile);

        // CREATE METADATA
        if (!$this->metadataExist($tableName)) {
            $this->addMetadata($webfile::$m__sClassName, '1', $tableName);
        }

        $attributeArray = $webfile->getAttributes();

        // CREATE DATABASETABLE
        $table = new MDatabaseTable(
            $this->databaseConnection,
            $tableName);
        $table->specifyIdentifier("id", 10);

        /** @var \ReflectionProperty $oAttribute */
        foreach ($attributeArray as $oAttribute) {

            $sAttributeName = $oAttribute->getName();

            if (MWebfile::isSimpleDatatype($sAttributeName)
                && MWebfile::getSimplifiedAttributeName($sAttributeName) != "id"
            ) {

                $prefix = substr($sAttributeName, 2, 1);
                if ($prefix == "s") {
                    $table->addColumn(
                        MWebfile::getSimplifiedAttributeName($sAttributeName),
                        MDatabaseDatatypes::VARCHAR,
                        50);
                } else if ($prefix == "l") {
                    $table->addColumn(
                        MWebfile::getSimplifiedAttributeName($sAttributeName),
                        MDatabaseDatatypes::VARCHAR,
                        2000);
                } else if ($prefix == "i") {
                    $table->addColumn(
                        MWebfile::getSimplifiedAttributeName($sAttributeName),
                        MDatabaseDatatypes::INT,
                        24);
                } else if ($prefix == "d") { //date
                    $table->addColumn(
                        MWebfile::getSimplifiedAttributeName($sAttributeName),
                        MDatabaseDatatypes::VARCHAR,
                        20);
                } else if ($prefix == "w") { //weekday
                    $table->addColumn(
                        MWebfile::getSimplifiedAttributeName($sAttributeName),
                        MDatabaseDatatypes::VARCHAR,
                        1);
                } else if ($prefix == "t") {
                    $table->addColumn(
                        MWebfile::getSimplifiedAttributeName($sAttributeName),
                        MDatabaseDatatypes::VARCHAR,
                        50);
                }
            }
        }
        if ($dropTableIfExists && $this->tableExistsByWebfile($webfile)) {
            $table->drop();
        }
        $table->create();


    }

    private function webfileExists(MWebfile $webfile)
    {

        if (!$this->tableExistsByWebfile($webfile)) {
            $this->createTable($webfile, false);
            return false;
        }

        $tableName = $this->getDatabaseTableName($webfile);

        $query = $this->databaseConnection->query("SELECT * FROM " . $tableName . " WHERE id='" . $webfile->getId() . "'");
        return ($query->num_rows > 0);

    }

    private function tableExistsByWebfile(MWebfile $webfile)
    {

        $tableName = $this->getDatabaseTableName($webfile);
        return $this->tableExistsByTablename($tableName);
    }

    private function tableExistsByTablename($tableName)
    {
        $allTableNames = $this->getAllTableNames();
        return in_array($tableName,$allTableNames);
    }

    /**
     * Returns all tablenames of the current connected database matching to the table prefix
     * in the used connection.
     */
    private function getAllTableNames()
    {

        $query = $this->databaseConnection->query("SHOW TABLES FROM " . $this->databaseConnection->getDatabase());

        $tableNames = array();

        if ($query->num_rows > 0) {
            while ($oDatabaseResultRow = $query->fetch_object()) {
                $tablesInVariableName = "Tables_in_" . $this->databaseConnection->getDatabase();
                // add only tables with the current connection prefix
                if (
                    substr(
                        $oDatabaseResultRow->$tablesInVariableName,
                        0,
                        strlen($this->databaseConnection->getTablePrefix()))
                    == $this->databaseConnection->getTablePrefix()) {

                    array_push($tableNames, $oDatabaseResultRow->Tables_in_webfiles);
                }

            }
        }

        return $tableNames;
    }

    /**
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getWebfilestream()
     */
    public function getWebfilesAsStream()
    {
        return new MWebfileStream($this->getWebfilesAsArray());
    }

    /**
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getWebfilesFromDatastore()
     */
    public function getWebfilesAsArray()
    {
        return $this->getByCondition();
    }

    /**
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::storeWebfile()
     * @return int Returns the id given in database (in case of a new webfile
     * the generated id will be returned)
     */
    /**
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::storeWebfile()
     * @param MWebfile $webfile
     * @return int Returns the id given in database (in case of a new webfile
     * the generated id will be returned)
     */
    public function storeWebfile(MWebfile $webfile)
    {
        if (!$this->tableExistsByWebfile($webfile)) {
            $this->createTable($webfile, false);
        }
        if (!$this->webfileExists($webfile)) {
            return $this->store($webfile);
        } else {
            return $this->update($webfile);
        }
    }


    public function getLatestWebfiles($count = 5)
    {

    }

    private function store(MWebfile $webfile, $useOnlySimpleDatatypes = false)
    {

        $tablename = $this->getDatabaseTableName($webfile);

        if (!$this->metadataExist($tablename)) {
            $this->addMetadata($webfile::$m__sClassName, '1', $tablename);
        }


        $oAttributeArray = $webfile->getAttributes();

        $sSqlFieldSetting = "";
        $sSqlValueSetting = "";

        $bIsFirstLoop = true;
        foreach ($oAttributeArray as $oAttribute) {
            $oAttribute->setAccessible(true);
            $sAttributeName = $oAttribute->getName();
            if ($sAttributeName != "m_iId" && (
                    MWebfile::isObject($sAttributeName) ||
                    MWebfile::isSimpleDatatype($sAttributeName))
            ) {

                if (!$bIsFirstLoop) {
                    $sSqlFieldSetting .= ",";
                    $sSqlValueSetting .= ",";
                }
                $sAttributeDatabaseFieldName = MWebfile::getSimplifiedAttributeName($sAttributeName);
                $sSqlFieldSetting .= $sAttributeDatabaseFieldName;
                if (MWebfile::isSimpleDatatype($sAttributeName)) {
                    $sSqlValueSetting .= "\"" . $oAttribute->getValue($webfile) . "\"";
                } else if (MWebfile::isObject($sAttributeName)) {

                    if (!$useOnlySimpleDatatypes) {
                        if ($this->$sAttributeName->getId() != 0) {
                            $this->$sAttributeName->update(1);
                            $sAttributeId = $this->$sAttributeName->getId();
                        } else {
                            $sAttributeId = $this->$sAttributeName->add(1);
                        }
                        $sSqlFieldSetting .= "id";
                        $sSqlValueSetting .= "\"" . $sAttributeId . "\"";
                    }
                }
                if ($bIsFirstLoop)
                    $bIsFirstLoop = false;
            }
        }

        $query = "INSERT INTO " . $tablename . " ( " . $sSqlFieldSetting . " ) VALUES ( " . $sSqlValueSetting . " )";
        echo $query;
        $this->databaseConnection->query($query);

        return $this->databaseConnection->getInsertId();

    }

    private function update(MWebfile $webfile, $useOnlySimpleDatatypes = false)
    {

        $oAttributeArray = $webfile->getAttributes();

        $setValuesString = "";
        $isFirstLoop = true;

        foreach ($oAttributeArray as $oAttribute) {
            $oAttribute->setAccessible(true);
            $sAttributeName = $oAttribute->getName();


            if ($sAttributeName != "m_iId" && (
                    MWebfile::isObject($sAttributeName) ||
                    MWebfile::isSimpleDatatype($sAttributeName))
            ) {

                if (!$isFirstLoop) {
                    $setValuesString .= ",";
                }
                $attributeDatabaseFieldName = MWebfile::getSimplifiedAttributeName($sAttributeName);
                $setValuesString .= $attributeDatabaseFieldName;
                if (MWebfile::isSimpleDatatype($sAttributeName)) {
                    $setValuesString .= " = '" . $oAttribute->getValue($webfile) . "'";
                } else if (MWebfile::isObject($sAttributeName)) {

                    if (!$useOnlySimpleDatatypes) {
                        if ($this->$sAttributeName->getId() != 0) {
                            $this->update($this->$sAttributeName, true);
                            $sAttributeId = $this->$sAttributeName->getId();
                        } else {
                            $sAttributeId = $this->store($this->$sAttributeName, true);
                        }
                        $setValuesString .= "_id";
                        $setValuesString .= " = \"" . $sAttributeId . "\"";
                    }
                }

                $isFirstLoop = false;
            }
        }

        $query = "UPDATE 
        			" . $this->getDatabaseTableName($webfile) . " 
        		 SET 
        			" . $setValuesString . " 
        		 WHERE 
        			id = '" . $webfile->getId() . "'";

        $this->databaseConnection->query($query);
        $error = $this->databaseConnection->getError();

        if (isset($error) && !empty($error)) {
            throw new MDatabaseDatastoreException($error, $query);
        }

        return $webfile->getId();

    }


    /**
     * Enter description here ...
     * @param MWebfile $webfile
     * @return string
     */
    public function getDatabaseTableName(MWebfile $webfile)
    {

        $classname = $webfile::$m__sClassName;

        if (strpos($classname, "\\") != -1) { // check if classname is given with namespace

            $lastBackslashOccurrence = strrpos($classname, "\\");
            $classname = substr($classname, $lastBackslashOccurrence + 1);
        }

        $tableName = $this->databaseConnection->getTablePrefix() . $classname;
        return $tableName;
    }

    public function resolveClassNameFromTableName($tableName)
    {
        if (!$this->tableExistsByTablename($this->databaseConnection->getTablePrefix() . "metadata")) {
            $this->createMetadataTable();
        }
        $metadata = $this->resolveMetadataForTablename($tableName);
        return $metadata->classname;
    }

    /**
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getByTemplate()
     * @param MWebfile $webfile
     * @return array
     */
    public function getByTemplate(MWebfile $webfile)
    {

        $webfileArray = array();

        if ($this->tableExistsByWebfile($webfile)) {

            // determine table with webfile type
            $tableName = $this->getDatabaseTableName($webfile);

            // translate template into a condition
            $condition = $this->translateTemplateIntoCondition($webfile);


            $first = true;
            $order = "";

            $attributes = $webfile->getAttributes(true);

            // SORTING
            foreach ($attributes as $attribute) {

                $attribute->setAccessible(true);

                $name = $attribute->getName();
                $value = $attribute->getValue($webfile);

                if ($value instanceof MAscendingSorting) {

                    if (!$first) {
                        $order .= " , ";
                    }
                    $order .= " " . MWebfile::getSimplifiedAttributeName($name) . " ASC ";
                    $first = false;
                } else if ($value instanceof MDescendingSorting) {

                    if (!$first) {
                        $order .= " , ";
                    }
                    $order .= " " . MWebfile::getSimplifiedAttributeName($name) . " DESC ";
                    $first = false;
                }
            }

            $query = "SELECT * FROM " . $tableName;


            if (!empty($condition)) {
                $query .= " WHERE " . $condition;
            }

            if (!empty($order)) {
                $query .= " ORDER BY " . $order;
            }

            $oDatabaseResultSet = $this->databaseConnection->query($query);

            if ($oDatabaseResultSet != false) {
                if ($oDatabaseResultSet->num_rows > 0) {
                    while ($databaseResultObject = $oDatabaseResultSet->fetch_object()) {

                        $className = $webfile::$m__sClassName;

                        $webfile = new $className();
                        foreach ($attributes as $oAttribute) {

                            $oAttribute->setAccessible(true);

                            $sAttributeName = $oAttribute->getName();
                            if (MWebfile::isSimpleDatatype($sAttributeName)) {
                                $sDatabaseFieldName = MWebfile::getSimplifiedAttributeName($sAttributeName);
                                $oAttribute->setValue($webfile, $databaseResultObject->$sDatabaseFieldName);
                            } else if (MWebfile::isObject($sAttributeName)) {
                                eval("\$sClassName = static::\$s__oAggregation[\$sAttributeName];");
                                /** @noinspection PhpUndefinedVariableInspection */
                                eval("\$oSubAttributeArray = $sClassName::getAttributes(1);");
                                /** @noinspection PhpUndefinedVariableInspection */
                                foreach ($oSubAttributeArray as $oSubAttribute) {

                                    $oSubAttributeName = $oSubAttribute->getName();
                                    if (MWebfile::isSimpleDatatype($oSubAttributeName)) {

                                        $sDatabaseFieldName = $this->getDatabaseTableName(new $tableName()) . "_" . MWebfile::getSimplifiedAttributeName($oSubAttributeName);
                                        $webfile->$sAttributeName->$oSubAttributeName = $databaseResultObject->$sDatabaseFieldName;
                                    }
                                }
                            }
                        }
                        array_push($webfileArray, $webfile);
                    }
                }
            }
        } else {
            $this->createTable($webfile, false);
        }

        return $webfileArray;

    }


    /**
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::deleteByTemplate()
     * @param MWebfile $webfile
     */
    public function deleteByTemplate(MWebfile $webfile)
    {

        if ($this->tableExistsByWebfile($webfile)) {

            // determine table with webfile type
            $tableName = $this->getDatabaseTableName($webfile);

            // translate template into a condition
            $condition = $this->translateTemplateIntoCondition($webfile);

            $query = "DELETE FROM " . $tableName;

            if (!empty($condition)) {
                $query .= " WHERE " . $condition;
            }

            $this->databaseConnection->query($query);
        }
    }

    /**
     *
     * @param MWebfile $webfile
     * @return string
     */
    private function translateTemplateIntoCondition(MWebfile $webfile)
    {

        $first = true;
        $condition = "";

        $attributes = $webfile->getAttributes(true);

        foreach ($attributes as $attribute) {

            $attribute->setAccessible(true);

            $name = $attribute->getName();
            $value = $attribute->getValue($webfile);

            if ($value != "?" && !($value instanceof MAscendingSorting)
                && !($value instanceof MDescendingSorting)
            ) {
                if (!$first) {
                    $condition .= " AND ";
                }

                if ($value instanceof MTimespan) {
                    $condition .= MWebfile::getSimplifiedAttributeName($name) . " BETWEEN '" . $value->getStart() . "' AND '" . $value->getEnd() . "'";
                } else if (!is_array($value)) {

                    $condition .= MWebfile::getSimplifiedAttributeName($name) . " = '" . $value . "'";

                } else {

                    $condition .= MWebfile::getSimplifiedAttributeName($name) . " IN (";

                    $firstInnerValue = true;
                    foreach ($value as $innerValue) {
                        if (!$firstInnerValue) {
                            $condition .= " , ";
                        }
                        $condition .= '\'' . $innerValue . '\'';
                        $firstInnerValue = false;
                    }
                    $condition .= ')';
                }
                $first = false;
            }
        }

        return $condition;
    }

    /**
     *
     */
    private function createMetadataTable()
    {

        $table = new MDatabaseTable(
            $this->databaseConnection,
            $this->databaseConnection->getTablePrefix() . 'metadata');
        $table->specifyIdentifier("id", 10);


        $table->addColumn(
            "classname",
            MDatabaseDatatypes::varchar(),
            250);
        $table->addColumn(
            "version",
            MDatabaseDatatypes::int(),
            50);
        $table->addColumn(
            "tablename",
            MDatabaseDatatypes::varchar(),
            250);

        $table->create();

    }

    private function metadataExist($tablename)
    {
        if (!$this->tableExistsByTablename($this->databaseConnection->getTablePrefix() . "metadata")) {
            $this->createMetadataTable();
            return false;
        }
        $oDatabaseResultSet = $this->databaseConnection->query("SELECT * FROM " . $this->databaseConnection->getTablePrefix() . "metadata WHERE tablename = '" . $tablename . "'");
        if ($oDatabaseResultSet->num_rows > 0) {
            return true;
        }
        return false;
    }

    private function addMetadata($className, $version, $tablename)
    {

        if (!$this->tableExistsByTablename($this->databaseConnection->getTablePrefix() . "metadata")) {
            $this->createMetadataTable();
        }
        $className = str_replace('\\', '\\\\', $className);
        $this->databaseConnection->query("INSERT INTO " . $this->databaseConnection->getTablePrefix() . "metadata (classname, version, tablename) VALUES ('" . $className . "' , '" . $version . "' , '" . $tablename . "');");
    }

    private function resolveMetadataForTablename($tablename)
    {

        if (!$this->tableExistsByTablename($this->databaseConnection->getTablePrefix() . "metadata")) {
            $this->createMetadataTable();
        }

        $oDatabaseResultSet = $this->databaseConnection->query("SELECT * FROM " . $this->databaseConnection->getTablePrefix() . "metadata WHERE tablename = '" . $tablename . "'");
        if ($oDatabaseResultSet->num_rows > 0) {
            $result = $oDatabaseResultSet->fetch_object();
            return $result;
        } else {
            return null;
        }
    }

}
