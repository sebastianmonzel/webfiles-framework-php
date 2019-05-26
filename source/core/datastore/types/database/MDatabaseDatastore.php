<?php

namespace webfilesframework\core\datastore\types\database;

use webfilesframework\core\datastore\MDatastoreException;
use webfilesframework\core\datasystem\database\MDatabaseTableColumn;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datastore\MAbstractDatastore;
use webfilesframework\core\datasystem\database\MDatabaseConnection;
use webfilesframework\core\datasystem\database\MDatabaseTable;
use webfilesframework\core\datasystem\database\MDatabaseDatatypes;
use webfilesframework\core\datastore\MISingleDatasourceDatastore;

use webfilesframework\core\datastore\functions\sorting\MAscendingSorting;
use webfilesframework\core\datastore\functions\sorting\MDescendingSorting;
use webfilesframework\core\datasystem\file\format\MWebfileStream;
use webfilesframework\core\time\MTimespan;
use webfilesframework\MWebfilesFrameworkException;

/**
 * Datastore based on a database. Actually only mysql is supported.
 * <br />
 * Store is devided in webfiles and metadata. webfiles represent the
 * content of data. Metadata contains a mapping from lassname to tablename
 * and the version of the webfile definition.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatabaseDatastore extends MAbstractDatastore
    implements MISingleDatasourceDatastore
{

    private $databaseConnection;

    const WEBFILEID = "webfileid";
    const TIME      = "time";
    const CLASSNAME = "classname";

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

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getWebfilestream()
	 * @return MWebfileStream
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    public function getWebfilesAsStream()
    {
        return new MWebfileStream($this->getWebfilesAsArray());
    }

	/**
	 * Returns als webfiles defined in datastore as array. (without looking for type of webfile)
	 *
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getWebfilesAsArray()
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 * @throws \Exception
	 */
    public function getWebfilesAsArray()
    {
        $webfilesArray = array();

        $metadataTableName = $this->databaseConnection->getTablePrefix() . "metadata";

        if (!$this->tableExists($metadataTableName)) {
            return $webfilesArray;
        }

        $oDatabaseResultHandler = $this->databaseConnection->queryAndHandle("SELECT * FROM " . $metadataTableName);
        if (!$oDatabaseResultHandler->getResultSize() > 0) {
            throw new \Exception("no tables given in metadata.");
        }
        while ( $result = $oDatabaseResultHandler->fetchNextResultObject() ) {
            $webfilesForTable = $this->getWebfilesByTableName($result->tablename, $result->classname);

            foreach ( $webfilesForTable as $webfile ) {
                array_push($webfilesArray, $webfile);
            }
        }
        return $webfilesArray;
    }

	/**
	 * @param MWebfile $webfile
	 *
	 * @return int Returns the id given in database (in case of a new webfile
	 * the generated id will be returned)
	 * @throws MDatabaseDatastoreException
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    public function storeWebfile(MWebfile $webfile)
    {

        if ( $webfile->getTime() == null ) {
            $webfile->setTime(time());
        }

        if (!$this->tableExistsByWebfile($webfile)) {
            $this->createTable($webfile, false);
            return $this->store($webfile);
        } else {
            if (!$this->webfileExists($webfile)) {
                return $this->store($webfile);
            } else {
                return $this->update($webfile);
            }
        }
    }

	/**
	 * @param MWebfile $webfile
	 * @param bool     $useOnlySimpleDatatypes
	 *
	 * @return int
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	private function store(MWebfile $webfile, $useOnlySimpleDatatypes = false)
	{

		$tablename = $this->resolveTableNameForWebfile($webfile);

		if (!$this->metadataExists($tablename)) {
			$this->addMetadata($webfile::classname(), '1', $tablename);
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
		$this->databaseConnection->query($query);

		return $this->databaseConnection->getInsertId();

	}

	/**
	 * @param MWebfile $webfile
	 * @param bool     $useOnlySimpleDatatypes
	 *
	 * @return int
	 * @throws MDatabaseDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
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
        			" . $this->resolveTableNameForWebfile($webfile) . " 
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
	 * @param MWebfile $webfile
	 *
	 * @return bool
	 * @throws MWebfilesFrameworkException
	 */
    private function tableExistsByWebfile(MWebfile $webfile)
    {
        $tableName = $this->resolveTableNameForWebfile($webfile);
        return $this->tableExists($tableName);
    }

    /**
     * resolve databasee table name by webfile object. table name scheme
     * is: $this->databaseConnection->getTablePrefix() . CLASSNAME_WITHOUT_NAMESPACE
     *
     * @param MWebfile $webfile
     * @return string
     */
    private function resolveTableNameForWebfile(MWebfile $webfile)
    {

        $classname = $webfile::classname();

        if (strpos($classname, "\\") != -1) { // check if classname is in global namespace or not

            $lastBackslashOccurrence = strrpos($classname, "\\");
            $classname = substr($classname, $lastBackslashOccurrence + 1);
        }

        $tableName = $this->databaseConnection->getTablePrefix() . $classname;
        return $tableName;
    }

	/**
	 * @param $tableName
	 *
	 * @return bool
	 * @throws MWebfilesFrameworkException
	 */
    private function tableExists($tableName)
    {
        $allTableNames = $this->getAllTableNames();
        return in_array($tableName,$allTableNames);
    }

	/**
	 * Returns all tablenames of the current connected database matching to the table prefix
	 * in the used connection. Also includes the meta table.
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
	 */
    private function getAllTableNames()
    {
        $handler = $this->databaseConnection->queryAndHandle("SHOW TABLES FROM `" . $this->databaseConnection->getDatabaseName() . "`");
        $tableNames = array();

        if ($handler->getResultSize() > 0) {
            while ($oDatabaseResultRow = $handler->fetchNextResultObject()) {
                $tablesInVariableName = "Tables_in_" . $this->databaseConnection->getDatabaseName();
                // add only tables with the current connection prefix
                if (
                    substr(
                        $oDatabaseResultRow->$tablesInVariableName,
                        0,
                        strlen($this->databaseConnection->getTablePrefix()))
                    == $this->databaseConnection->getTablePrefix()) {

                    array_push($tableNames, $oDatabaseResultRow->$tablesInVariableName);
                }

            }
        }

        return $tableNames;
    }

	/**
	 * Creates a database table to persist objects of this type.
	 * @param MWebfile $webfile
	 * @param bool     $dropTableIfExists
	 *
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    private function createTable(MWebfile $webfile, $dropTableIfExists = true)
    {

        $tableName = $this->resolveTableNameForWebfile($webfile);

        // CREATE METADATA
        if (!$this->metadataExists($tableName)) {
            $this->addMetadata($webfile::classname(), '1', $tableName);
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
                // ignore id as long as its set seperatly as primary key directly after creation of table
            ) {

                $tableColum = $this->createTableColumnFromAttributeName($sAttributeName);
                $table->addColumnObject($tableColum);
            }
        }
        if ($dropTableIfExists && $this->tableExistsByWebfile($webfile)) {
            $table->drop();
        }
        $table->create();
    }

	/**
	 * @param $sAttributeName
	 *
	 * @return MDatabaseTableColumn
	 * @throws MDatastoreException
	 */
    private function createTableColumnFromAttributeName($sAttributeName)
    {
        $prefix = substr($sAttributeName, 2, 1);
        if ($prefix == "s") { // string (width = 50)
            return new MDatabaseTableColumn(
                MWebfile::getSimplifiedAttributeName($sAttributeName),
                MDatabaseDatatypes::VARCHAR,
                50);
        } else if ($prefix == "l") { // longtext (width = 2000)
            return new MDatabaseTableColumn(
                MWebfile::getSimplifiedAttributeName($sAttributeName),
                MDatabaseDatatypes::VARCHAR,
                2000);
        } else if ($prefix == "i") { // int
            return new MDatabaseTableColumn(
                MWebfile::getSimplifiedAttributeName($sAttributeName),
                MDatabaseDatatypes::INT,
                24);
        } else if ($prefix == "d") { //date
            return new MDatabaseTableColumn(
                MWebfile::getSimplifiedAttributeName($sAttributeName),
                MDatabaseDatatypes::VARCHAR,
                20);
        } else if ($prefix == "w") { //weekday
            return new MDatabaseTableColumn(
                MWebfile::getSimplifiedAttributeName($sAttributeName),
                MDatabaseDatatypes::VARCHAR,
                1);
        } else if ($prefix == "t") {
            return new MDatabaseTableColumn(
                MWebfile::getSimplifiedAttributeName($sAttributeName),
                MDatabaseDatatypes::VARCHAR,
                50);
        } else {
            throw new MDatastoreException(
                "Unknown datatype prefix '" . $prefix . "' for database datastore on attribute name '" . $sAttributeName . "'.");
        }
    }


	/**
	 * @param MWebfile $webfile
	 *
	 * @return bool
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    private function webfileExists(MWebfile $webfile)
    {

        if (!$this->tableExistsByWebfile($webfile)) {
            $this->createTable($webfile, false);
            return false;
        }

        $tableName = $this->resolveTableNameForWebfile($webfile);

        $query = $this->databaseConnection->queryAndHandle(
            "SELECT * FROM " . $tableName . " WHERE id='" . $webfile->getId() . "'");
        return ($query->getResultSize() > 0);

    }

	/**
	 * @param int $count
	 *
	 * @return array|void
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    public function getLatestWebfiles($count = 5)
    {

        $result = array();

        $handler = $this->databaseConnection->queryAndHandle(
            "SELECT webfileid,time,classname FROM " .
            $this->databaseConnection->getTablePrefix() . "metadatanormalization" .
            "ORDER BY time DESC LIMIT " . $count);// TODO prevent sql injection

        while ($object = $handler->fetchNextResultObject() ) {

            $webfile = $this->transformMetadataObjectToWebfile($object);
            $result = $this->addWebfileSafetyToArray($webfile,$result);
        }

    }

	/**
	 * @param $timestamp
	 *
	 * @return mixed|null
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    public function getNextWebfileForTimestamp($timestamp)
    {

        $handler = $this->databaseConnection->queryAndHandle(
            "SELECT webfileid,time,classname FROM " .
            $this->databaseConnection->getTablePrefix() . "metadatanormalization" .
            "WHERE time > " . $timestamp . " ORDER BY time DESC LIMIT 1");// TODO prevent sql injection

        if ( $handler->getResultSize() == 0 ) {
            return null;
        }

        $object = $handler->fetchNextResultObject();
        return $this->transformMetadataObjectToWebfile($object);
    }


	/**
	 * @param $classname
	 *
	 * @return MWebfile
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    private function createWebfileByClassname($classname) {
        $ref = new \ReflectionClass($classname);
        $webfile = $ref->newInstanceWithoutConstructor();
        if (! $webfile instanceof MWebfile ) {
            throw new MWebfilesFrameworkException("given class '" . $classname . " does not extend MWebfile.");
        }
        return $webfile;
    }

	/**
	 * @param $tableName
	 *
	 * @return mixed
	 * @throws MWebfilesFrameworkException
	 */
    public function resolveClassNameFromTableName($tableName)
    {
        if (! $this->metadataTableExists() ) {
            $this->createMetadataTable();
        }
        $metadata = $this->resolveMetadataForTablename($tableName);
        return $metadata->classname;
    }

	/**
	 * @param $className
	 * @param $version
	 * @param $tablename
	 *
	 * @throws MWebfilesFrameworkException
	 */
	private function addMetadata($className, $version, $tablename)
	{

		if (! $this->metadataTableExists() ) {

			$this->createMetadataTable();
		}
		$className = str_replace('\\', '\\\\', $className);
		$this->databaseConnection->query(
			"INSERT INTO " .
			$this->databaseConnection->getTablePrefix() . "metadata" .
			"(classname, version, tablename)" .
			" VALUES ('" . $className . "' , '" . $version . "' , '" . $tablename . "');");
	}

	/**
	 * @return bool
	 * @throws MWebfilesFrameworkException
	 */
	private function metadataTableExists() {
		return $this->tableExists( $this->databaseConnection->getTablePrefix() . "metadata" );
	}

	/**
	 * @param $tablename
	 *
	 * @return bool
	 * @throws MWebfilesFrameworkException
	 */
	private function metadataExists($tablename)
	{
		if (! $this->metadataTableExists() ) {
			$this->createMetadataTable();
			return false;
		}
		$oDatabaseResultSet = $this->databaseConnection->queryAndHandle(
			"SELECT * FROM " . $this->databaseConnection->getTablePrefix() . "metadata WHERE tablename = '" . $tablename . "'");
		if ($oDatabaseResultSet->getResultSize() > 0) {
			return true;
		}
		return false;
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
			self::CLASSNAME,
			MDatabaseDatatypes::VARCHAR,
			250);
		$table->addColumn(
			"version",
			MDatabaseDatatypes::INT,
			50);
		$table->addColumn(
			"tablename",
			MDatabaseDatatypes::VARCHAR,
			250);

		$table->create();

	}


	/**
	 * @param $tablename
	 *
	 * @return object|\stdClass|null
	 * @throws MWebfilesFrameworkException
	 */
    private function resolveMetadataForTablename($tablename)
    {

        if ( ! $this->metadataTableExists() ) {
            $this->createMetadataTable();
        }

        $oDatabaseResultHandler = $this->databaseConnection->queryAndHandle(
            "SELECT * FROM " . $this->databaseConnection->getTablePrefix() . "metadata WHERE tablename = '" . $tablename . "'");

        if ($oDatabaseResultHandler->getResultSize() > 0) {
            $result = $oDatabaseResultHandler->fetchNextResultObject();
            return $result;
        } else {
            return null;
        }
    }

	/**
	 * @param $object
	 *
	 * @return mixed
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	private function transformMetadataObjectToWebfile($object) {

		$template = $this->createWebfileByClassname($object->classname);
		$template->presetForTemplateSearch();
		$template->setId($object->webfileid);

		$webfiles = $this->searchByTemplate($template);
		return $webfiles[0];
	}


	/**
	 * @param MWebfile $template
	 *
	 * @return array
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    public function searchByTemplate(MWebfile $template)
    {

        $webfileArray = array();

        if ($this->tableExistsByWebfile($template)) {

            $tableName = $this->resolveTableNameForWebfile($template);

            $sorting = $this->translateTemplateIntoSorting($template);
            $condition = $this->translateTemplateIntoCondition($template);

            $webfileArray = $this->getWebfilesByTablename(
                $tableName,$template::classname(),$condition,$sorting
            );

        } else {
            $this->createTable($template, false);
        }

        return $webfileArray;
    }

	/**
	 * @param      $tableName
	 * @param null $className
	 * @param null $condition
	 * @param null $order
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    private function getWebfilesByTablename($tableName,$className = null,$condition = null,$order = null)
    {
        $webfileArray = array();


        $query = "SELECT * FROM " . $tableName;
        if (!empty($condition)) {
            $query .= " WHERE " . $condition;
        }

        if (!empty($order)) {
            $query .= " ORDER BY " . $order;
        }

        $resultHandler = $this->databaseConnection->queryAndHandle($query);

        if ($resultHandler != false) {
            if ($resultHandler->getResultSize() > 0) {
                while ($databaseResultObject = $resultHandler->fetchNextResultObject()) {
                    if ( $className == null ) {
                        $className = $this->resolveClassNameFromTableName($tableName);
                    }
                    /** @var MWebfile $targetWebfile */
                    $targetWebfile = new $className();
                    $attributes = $targetWebfile->getAttributes(true);

                    foreach ($attributes as $oAttribute) {

                        $oAttribute->setAccessible(true);

                        $sAttributeName = $oAttribute->getName();
                        if (MWebfile::isSimpleDatatype($sAttributeName)) {
                            $sDatabaseFieldName = MWebfile::getSimplifiedAttributeName($sAttributeName);
                            $oAttribute->setValue($targetWebfile, $databaseResultObject->$sDatabaseFieldName);
                        } else if (MWebfile::isObject($sAttributeName)) {
                            eval("\$sClassName = static::\$s__oAggregation[\$sAttributeName];");
                            /** @noinspection PhpUndefinedVariableInspection */
                            eval("\$oSubAttributeArray = $sClassName::getAttributes(1);");
                            /** @noinspection PhpUndefinedVariableInspection */
                            foreach ($oSubAttributeArray as $oSubAttribute) {

                                $oSubAttributeName = $oSubAttribute->getName();
                                if (MWebfile::isSimpleDatatype($oSubAttributeName)) {

                                    $sDatabaseFieldName = $this->resolveTableNameForWebfile(
                                        new $tableName()) . "_" . MWebfile::getSimplifiedAttributeName($oSubAttributeName);
                                    $targetWebfile->$sAttributeName->$oSubAttributeName = $databaseResultObject->$sDatabaseFieldName;
                                }
                            }
                        }
                    }

                    $webfileArray = $this->addWebfileSafetyToArray($targetWebfile,$webfileArray);
                }
            }
        }
        return $webfileArray;
    }

	/**
	 * @param MWebfile $webfile
	 *
	 * @return string
	 * @throws \ReflectionException
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
	 * @param MWebfile $template
	 *
	 * @return string
	 * @throws \ReflectionException
	 */
    private function translateTemplateIntoSorting(MWebfile $template)
    {
        $attributes = $template->getAttributes(true);

        $order = "";
        $first = true;

        foreach ($attributes as $attribute) {

            $attribute->setAccessible(true);

            $name = $attribute->getName();
            $value = $attribute->getValue($template);

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
        return $order;
    }

	/**
	 * Normalizes database datastore:
	 *  - collect all webfiles in one table to sort after timestamp over all
	 *  -
	 *
	 * @param bool $useHumanReadableTimestamps
	 * @param bool $saveThumbnailsForImages
	 *
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    public function normalize($useHumanReadableTimestamps = false, $saveThumbnailsForImages = false) {

        $webfiles = $this->getWebfilesAsArray();

        /** @var MWebfile $webfile */
        foreach ($webfiles as $webfile) {
            $this->addMetadataNormalizationEntry(
                $webfile->getId(), $webfile->getTime(), $webfile::classname());
        }

    }

	private function createMetadataNormalizationTable()
	{

		$table = new MDatabaseTable(
			$this->databaseConnection,
			$this->databaseConnection->getTablePrefix() . 'metadatanormalization');
		$table->specifyIdentifier("id", 10);

		$table->addColumn(
			self::WEBFILEID,
			MDatabaseDatatypes::VARCHAR,
			250);
		$table->addColumn(
			self::TIME,
			MDatabaseDatatypes::INT,
			12);
		$table->addColumn(
			self::CLASSNAME,
			MDatabaseDatatypes::VARCHAR,
			250);

		$table->create();

	}

	/**
	 * @param $webfileid
	 * @param $time
	 * @param $classname
	 *
	 * @throws MWebfilesFrameworkException
	 */
	private function addMetadataNormalizationEntry($webfileid, $time, $classname) {

		if (!$this->tableExists(
			$this->databaseConnection->getTablePrefix() . "metadatanormalization")) {

			$this->createMetadataNormalizationTable();
		}
		$className = str_replace('\\', '\\\\', $classname);
		$sqlCommand = "INSERT INTO " .
		              $this->databaseConnection->getTablePrefix() . "metadatanormalization " .
		              "(webfileid, time, classname)" .
		              " VALUES ('" . $webfileid . "' , " . $time . " , '" . $className . "');";
		$this->databaseConnection->query(
			$sqlCommand);
		echo "\n\n" . $this->databaseConnection->getError() . "\n\n";
	}

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::deleteByTemplate()
	 *
	 * @param MWebfile $webfile
	 *
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	public function deleteByTemplate(MWebfile $webfile)
	{

		if ($this->tableExistsByWebfile($webfile)) {

			// determine table with webfile type
			$tableName = $this->resolveTableNameForWebfile($webfile);

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
	 * Deletes all webfiles in the store and all metadata
	 *
	 * @throws MWebfilesFrameworkException
	 */
    public function deleteAll() {

        $tablenames = $this->getAllTableNames();
        foreach ($tablenames as $tablename) {
            $this->databaseConnection->query("DROP TABLE " . $tablename);
        }
    }

}
