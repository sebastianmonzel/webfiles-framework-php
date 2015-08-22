<?php

namespace simpleserv\webfilesframework\core\datastore\types\database;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection;
use simpleserv\webfilesframework\core\datasystem\database\MDatabaseTable;
use simpleserv\webfilesframework\core\datasystem\database\MIDbDatatypes;
use simpleserv\webfilesframework\core\datastore\MISingleDatastore;
use simpleserv\webfilesframework\core\datastore\functions\filter\MSubstringFiltering;

/**
 * Class to connect to a datastore based on a database.
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatabaseDatastore extends MAbstractDatastore 
							implements MISingleDatastore {
	
	private $databaseConnection;
	
	public function __construct(MDatabaseConnection $databaseConnection) {
		$this->databaseConnection = $databaseConnection;
	}
	
	public function tryConnect() {
		return $this->databaseConnection->connect();
	}
	
	public function isReadOnly() {
		return false;
	}
	
	public function getNextWebfileForTimestamp($time) {
		
	}
	
	public function getTime() {
		return NULL;
	}
	
	public function getGeograficPosition() {
		return NULL;
	}
	
	/**
	 * Creates a database table to persist objects of this type.
	 * @param MWebfile $webfile
	 * @param boolean $dropTableIfExists
	 */
    private function createTable(MWebfile $webfile, $dropTableIfExists = true) {
    	
    	$sAttributeArray = $webfile->getAttributes();
    	
    	$table = new MDatabaseTable(
    						$this->databaseConnection ,
    						$this->getDatabaseTableName($webfile));
    	$table->setIdentifier("id", 10);
    	
    	foreach ( $sAttributeArray as $oAttribute ) {
    		
    		$sAttributeName = $oAttribute->getName();
    		
    		if ( MWebfile::isSimpleDatatype($sAttributeName)
    				&& MWebfile::getSimplifiedAttributeName($sAttributeName) != "id") {
    			
    			$prefix = substr($sAttributeName, 2,1);
    			if ( $prefix == "s" ) {
    				$table->addColumn(
    							MWebfile::getSimplifiedAttributeName($sAttributeName), 
    							MIDbDatatypes::varchar(),
    							50);
    			} else if ( $prefix == "l" ) {
    				$table->addColumn(
    							MWebfile::getSimplifiedAttributeName($sAttributeName), 
    							MIDbDatatypes::varchar(),
    							2000);
    			} else if ( $prefix == "i" ) {
    				$table->addColumn(
    							MWebfile::getSimplifiedAttributeName($sAttributeName), 
    							MIDbDatatypes::int(),
    							20);
    			} else if ( $prefix == "t" ) {
    				$table->addColumn(
    							MWebfile::getSimplifiedAttributeName($sAttributeName), 
    							MIDbDatatypes::varchar(),
    							50);
    			}
    		}
    	}
    	if ( $dropTableIfExists && $this->tableExistsByWebfile($webfile) ) {
	    	$table->drop();
    	}
    	$table->create();
    	
    }
    
    private function webfileExists(MWebfile $webfile) {
    	
    	if ( ! tableExists($webfile) ) {
    		return false;
    	}
    	
    	$tableName = $this->getDatabaseTableName($webfile);
	    
	   	$query = $this->databaseConnection->query("SELECT * FROM " . $tableName . " WHERE id='" . $webfile->getId() . "'");
	   	return ( $query->num_rows > 0 );
    	
    }
    
    private function tableExistsByWebfile(MWebfile $webfile) {
    	
    	$tableName = $this->getDatabaseTableName($webfile);
    	return $this->tableExistsByTablename($tableName);
    }
    
    private function tableExistsByTablename($tableName) {
    	 
    	$query = $this->databaseConnection->query("SHOW TABLES FROM `" . $this->databaseConnection->getDatabase() . "`");
    
    	while ( $oDatabaseResultRow = $query->fetch_object() ) {
    
    		$tablesInVariableName = "Tables_in_" . $this->databaseConnection->getDatabase();
    
    		if ( $oDatabaseResultRow->$tablesInVariableName == $tableName ) {
    			return true;
    		}
    		
    	}
    	return false;
    }
    
    /**
     * Returns all tablenames of the current connected database matching to the table prefix
     * in the used connection.
     */
    private function getAllTableNames() {
    	
    	$query = $this->databaseConnection->query("SHOW TABLES FROM " . $this->databaseConnection->getDatabase());
    	
    	$tableNames = array();
    	
    	if ($query->num_rows > 0) {
		    while ( $oDatabaseResultRow = $query->fetch_object() ) {
		    	
		    	// add only tables with the current connection prefix
		    	if ( substr($oDatabaseResultRow->Tables_in_webfiles, 0, strlen($this->databaseConnection->getTablePrefix())) == $this->databaseConnection->getTablePrefix() ) {	    		
			    	array_push($tableNames, $oDatabaseResultRow->Tables_in_webfiles);
		    	}
		    	
		    }
    	}
    	
    	return $tableNames;
    }
    
    /**
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getWebfilestream()
     */
    public function getWebfilesAsStream() {
    	return new MWebfileStream($this->getWebfilesAsArray());
    }
    
    /**
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getWebfilesFromDatastore()
     */
	public function getWebfilesAsArray() {
		return $this->getByCondition();
	}
	
	/**
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::storeWebfile()
	 * @return Returns the id given in database (in case of a new webfile 
	 * the generated id will be returned)
	 */
	public function storeWebfile(MWebfile $webfile) {
		if ( ! $this->tableExistsByWebfile($webfile) ) {
			$this->createTable($webfile);
		}
		if ( ! $this->webfileExists($webfile) ) {
			return $this->store($webfile);
		} else {
			return $this->update($webfile);
		}
	}
	

	public function getLatestWebfiles($count = 5) {
	
	}
	
	private function store(MWebfile $webfile, $useOnlySimpleDatatypes = false) {
		
		$tablename = $this->getDatabaseTableName($webfile);
		
		if ( ! $this->metadataExist($tablename) ) {
			$this->addMetadata($webfile::$m__sClassName, '1', $tablename);
		}
		
		
		$oAttributeArray = $webfile->getAttributes();
    	
        $sSqlFieldSetting = "";        
        $sSqlValueSetting = "";
		
        $bIsFirstLoop = true;
        foreach ($oAttributeArray as $oAttribute) {
        	$oAttribute->setAccessible(true);
        	$sAttributeName = $oAttribute->getName();
        	if ( $sAttributeName != "m_iId" && (
        			MWebfile::isObject($sAttributeName) || 
        			MWebfile::isSimpleDatatype($sAttributeName) ) ) {
	        	
        		if ( ! $bIsFirstLoop ) {
	        		$sSqlFieldSetting .= ",";
	        		$sSqlValueSetting .= ",";
	        	}
	        	$sAttributeDatabaseFieldName = MWebfile::getSimplifiedAttributeName($sAttributeName);
	        	$sSqlFieldSetting .= $sAttributeDatabaseFieldName;
	        	if (MWebfile::isSimpleDatatype($sAttributeName)) {
		        	$sSqlValueSetting .= "\"" . $oAttribute->getValue($webfile) . "\"";
	        	} else if (MWebfile::isObject($sAttributeName)) {
	        		
					if ( ! $useOnlySimpleDatatypes ) {
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
        
        $query = "INSERT INTO ". $tablename . " ( " . $sSqlFieldSetting . " ) VALUES ( " . $sSqlValueSetting . " )";
        $this->databaseConnection->query($query);
        
        return $this->databaseConnection->getInsertId();

    }
	
	private function update(MWebfile $webfile, $useOnlySimpleDatatypes = false) {
    	
		$oAttributeArray = $webfile->getAttributes();
    	
        $setValuesString = "";
        $isFirstLoop = true;
       
        foreach ($oAttributeArray as $oAttribute) {
        	$oAttribute->setAccessible(true);
        	$sAttributeName = $oAttribute->getName();
        	
        	
        	if ( $sAttributeName != "m_iId" && (
        			MWebfile::isObject($sAttributeName) || 
        			MWebfile::isSimpleDatatype($sAttributeName) ) ) {
	        	
        		if ( ! $isFirstLoop ) {
	        		$setValuesString .= ",";
	        	}
	        	$attributeDatabaseFieldName = MWebfile::getSimplifiedAttributeName($sAttributeName);
	        	$setValuesString .= $attributeDatabaseFieldName;
	        	if (MWebfile::isSimpleDatatype($sAttributeName)) {
		        	$setValuesString .= " = '" . $oAttribute->getValue($webfile) . "'";
	        	} else if (MWebfile::isObject($sAttributeName)) {
	        		
					if ( ! $useOnlySimpleDatatypes ) {
						if ($this->$sAttributeName->getId() != 0) {
							$this->update($this->$sAttributeName,true);
							$sAttributeId = $this->$sAttributeName->getId();
						} else {
							$sAttributeId = $this->store($this->$sAttributeName,true);
						}
						$setValuesString .= "_id";
		        		$setValuesString .= " = \"" . $sAttributeId . "\"";
					}
	        	}
	        	
	        	$isFirstLoop = false;
        	}
        }
        
        $query = "UPDATE 
        			". $this->getDatabaseTableName($webfile) . " 
        		 SET 
        			" . $setValuesString . " 
        		 WHERE 
        			id = '" . $webfile->getId() . "'";
        
        $this->databaseConnection->query($query);
        $error = $this->databaseConnection->getError();
        
        if ( isset($error) && ! empty($error) ) {
        	throw new MDatabaseDatastoreException($error,$query);
        }
        
        return $webfile->getId();

    }
    
	
	/**
     * Enter description here ...
     * @param MWebfile $webfile
     */
    public function getDatabaseTableName(MWebfile $webfile) {
   		
    	$classname = $webfile::$m__sClassName;
    	
    	if ( strpos($classname, "\\") != -1 ) { // check if classname is given with namespace
    		
    		$lastBackslashOccurrence = strrpos($classname, "\\");
    		$classname = substr($classname, $lastBackslashOccurrence+1);
    	} 
    	
   		$tableName = $this->databaseConnection->getTablePrefix() . $classname;
    	return $tableName;
    }
    
    public function resolveClassNameFromTableName($tableName) {
    	$metadata = $this->getMetadataForTablename($tableName);
    	return $metadata->classname;
    }
	
    /**
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getByTemplate()
     */
    public function getByTemplate(MWebfile $webfile) {
    	
    	$webfileArray = array();
    	
    	if ( $this->tableExistsByWebfile($webfile) ) {
    	
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
	    	
	    		$name  = $attribute->getName();
	    		$value = $attribute->getValue($webfile);
	    		
	    		if ( $value instanceof MAscendingSorting ) {
	    			
	    			if ( ! $first ) {
	    				$order .= " , ";
	    			}
	    			$order .= " " . MWebfile::getSimplifiedAttributeName($name) . " ASC ";
	    			$first = false;
	    		} else if (  $value instanceof MDescendingSorting ) {
	    			
	    			if ( ! $first ) {
	    				$order .= " , ";
	    			}
	    			$order .= " " . MWebfile::getSimplifiedAttributeName($name) . " DESC ";
	    			$first = false;
	    		}
	    	}
	    	
	    	$query = "SELECT * FROM " . $tableName;
	    	
	    	
	    	if ( !empty($condition) ) {
	    		$query .= " WHERE " . $condition;
	    	}
	    	
	    	if ( !empty($order) ) {
	    		$query .= " ORDER BY " . $order;
	    	}
	    	
	    	$oDatabaseResultSet = $this->databaseConnection->query($query);
			
	    	if ($oDatabaseResultSet != false) {
	    		if ($oDatabaseResultSet->num_rows > 0) {
				    while ( $databaseResultObject = $oDatabaseResultSet->fetch_object() ) {
			    		
			    		$className = $this->resolveClassNameFromTableName($tableName);
			    		
			    		$webfile = new $className();
			    		foreach ($attributes as $oAttribute) {
			    			
			    			$oAttribute->setAccessible(true);
			    			
			    			$sAttributeName = $oAttribute->getName();
			    			if (MWebfile::isSimpleDatatype($sAttributeName)) {
				    			$sDatabaseFieldName = MWebfile::getSimplifiedAttributeName($sAttributeName);
			    				$oAttribute->setValue($webfile,$databaseResultObject->$sDatabaseFieldName);
			    			} else if (MWebfile::isObject($sAttributeName)) {
			    				eval("\$sClassName = static::\$s__oAggregation[\$sAttributeName];");
			    				eval("\$oSubAttributeArray = $sClassName::getAttributes(1);");
			    				foreach($oSubAttributeArray as $oSubAttribute) {
			    					
			    					$oSubAttributeName = $oSubAttribute->getName();
			    					if ( MWebfile::isSimpleDatatype($oSubAttributeName) ) {
			    						
				    					$sDatabaseFieldName = $this->getDatabaseTableName(new $tableName()) . "_" . MWebfile::getSimplifiedAttributeName($oSubAttributeName);
			    						$webfile->$sAttributeName->$oSubAttributeName = $databaseResultObject->$sDatabaseFieldName;
			    					}
			    				}
			    			}
			    		}
			    		array_push($webfileArray,$webfile); 
			    	}
		    	}
	    	}
    	}
    	
    	return $webfileArray;
    	
    }
    
    /**
     * Fetches all representatives of persistent objects of this type matching the given condition
     * @param $condition condition to fetch objects
     * @return all representatives of persisted objects matching the given condition
     */
    /*public function getByCondition($condition = "") {
    	
    	$tableNames = $this->getAllTableNames();
    	
    	// TODO replace name with name derived from table name
        $sAttributeArray = MBlogEntry::getAttributes();
        
    	$sSqlSelectFields = "";
    	$sSqlJoins = "";
    	
    	$bIsFirst = 1;
    	foreach ( $tableNames as $tableName ) {
    		$className = $this->resolveClassNameFromTableName($tableName);
    		
	    	foreach ( $sAttributeArray as $oAttribute ) {
	    		$sAttributeName = $oAttribute->getName();
	    		if ( ! $bIsFirst && (MWebfile::isSimpleDatatype($sAttributeName) || MWebfile::isObject($sAttributeName) )  ) {
		    			$sSqlSelectFields .= ",";
		    	}
	    		if (MWebfile::isSimpleDatatype($sAttributeName)) {
	    			
	    			//is attribute of this item
	    			//select fields from table of this item
	    			
	    			$sSqlSelectFields .= $this->getDatabaseTableName(new $className()) . 
	    						"."  . 
	    						MWebfile::getSimplifiedAttributeName($sAttributeName) . 
	    						" " . 
	    						$this->getDatabaseTableName(new $className()) . 
	    						"_" . 
	    						MWebfile::getSimplifiedAttributeName($sAttributeName);
	    		} else if (MWebfile::isObject($sAttributeName)) {
	    			//is subitem
	    			eval("\$oDatabaseItemName = static::\$s__oAggregation[\$sAttributeName];");
	    			
	    			$oDatabaseItem = new $oDatabaseItemName();
	    			
	    			$oJoinTableName = self::getDatabaseTableName($oDatabaseItemName);
	    			eval("\$oJoinAttributeArray = " . $oDatabaseItemName . "::getAttributes();");
					
	    			//select fields from table of subitem
	    			$bSubIsFirst = 1;
	    			foreach ( $oJoinAttributeArray as $oJoinAttribute ) {
	    				$sJoinAttributeName = $oJoinAttribute->getName();
	    				
	    				if (MWebfile::isObject($sJoinAttributeName) || self::isSimpleDatatype($sJoinAttributeName) ) {
		    				if (!$bSubIsFirst) {    					
		    							$sSqlSelectFields .= ",";
		    				}
	    					if (MWebfile::isObject($sJoinAttributeName))
	    						$sSqlSelectFields .= $oJoinTableName . "." . MWebfile::getSimplifiedAttributeName($sJoinAttributeName) . "id " . $oJoinTableName . "_" . self::getDatabaseFieldName($sJoinAttributeName) . "id  ";
	    					else if (MWebfile::isSimpleDatatype($sJoinAttributeName))
	    						$sSqlSelectFields .= $oJoinTableName . "." . MWebfile::getSimplifiedAttributeName($sJoinAttributeName) . " " . $oJoinTableName . "_" . self::getDatabaseFieldName($sJoinAttributeName) . "  ";
		    				if ($bSubIsFirst) {
				    			$bSubIsFirst = 0;
				    		}
	    				}
			    		
	    			}
	    			//create joins to table of subitem
	    			$sSqlJoins .= " LEFT JOIN " . $oJoinTableName . " ON " . $this->getDatabaseTableName() . "." . $oJoinTableName . "id" . " = " . $oJoinTableName . ".id ";
	    			
	    		}
	    		if ($bIsFirst && (MWebfile::isSimpleDatatype($sAttributeName) || self::isObject($sAttributeName) )) {
	    			$bIsFirst = 0;
	    		}
	    	}
    	}
    	
    	$sSqlQuery = "SELECT " . $sSqlSelectFields . " FROM " . $this->getDatabaseTableName(new MBlogEntry()) . $sSqlJoins;

    	if ( $condition != "" ) {
    		$sSqlQuery .= " WHERE " . $condition;
    	}
    	
    	//array for saving result
    	$webfileArray = array();
    	
    	$oDatabaseResultSet = $this->databaseConnection->query($sSqlQuery);
		
    	$this->databaseConnection->printError();
    	    	
    	if ($oDatabaseResultSet != false) {
    		if ($oDatabaseResultSet->num_rows > 0) {
			    while ( $oDatabaseResultRow = $oDatabaseResultSet->fetch_object() )
		    	{
		    		//var_export($oDatabaseResultRow);
		    		
		    		//TODO replace with classname derived by tablename
		    		$webfile = new $className();
		    		foreach ($sAttributeArray as $oAttribute) {
		    			
		    			$oAttribute->setAccessible(true);
		    			
		    			$sAttributeName = $oAttribute->getName();
		    			if (MWebfile::isSimpleDatatype($sAttributeName)) {
			    			$sDatabaseFieldName = $this->getDatabaseTableName(new $className()) . "_" . MWebfile::getSimplifiedAttributeName($sAttributeName);
		    				$oAttribute->setValue($webfile,$oDatabaseResultRow->$sDatabaseFieldName);
		    			} else if (MWebfile::isObject($sAttributeName)) {
		    				eval("\$sClassName = static::\$s__oAggregation[\$sAttributeName];");
		    				eval("\$oSubAttributeArray = $sClassName::getAttributes(1);");
		    				foreach($oSubAttributeArray as $oSubAttribute)
		    				{
		    					$oSubAttributeName = $oSubAttribute->getName();
		    					if ( MWebfile::isSimpleDatatype($oSubAttributeName) ) {
		    						
			    					$sDatabaseFieldName = $this->getDatabaseTableName(new $tableName()) . "_" . MWebfile::getSimplifiedAttributeName($oSubAttributeName);
		    						$webfile->$sAttributeName->$oSubAttributeName = $oDatabaseResultRow->$sDatabaseFieldName;
		    					}
		    				}
		    			}
		    		}
		    		array_push($webfileArray,$webfile); 
		    	}
	    	} else {
	    		return false;
	    	}
    	}else {
    		return false;
    	}
    	return $webfileArray;
    }*/
    
	/**
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::deleteByTemplate()
	 */
    public function deleteByTemplate(MWebfile $webfile) {
    	
    	
    	if ( $this->tableExistsByWebfile($webfile) ) {
    		 
    		// determine table with webfile type
    		$tableName = $this->getDatabaseTableName($webfile);
    	
    		// translate template into a condition
    		$condition = $this->translateTemplateIntoCondition($webfile);
    		
    		$query = "DELETE FROM " . $tableName;
    	
    		if ( !empty($condition) ) {
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
    private function translateTemplateIntoCondition(MWebfile $webfile) {
    	
    	$first = true;
    	$condition = "";
    	 
    	$attributes = $webfile->getAttributes(true);
    	 
    	foreach ($attributes as $attribute) {
    	
    		$attribute->setAccessible(true);
    	
    		$name  = $attribute->getName();
    		$value = $attribute->getValue($webfile);
    	
    		if ( $value != "?" ) {
    			if ( ! $first ) {
    				$condition .= " AND ";
    			}
    			 
    			if ( ! is_array($value) ) {
    				$condition .= MWebfile::getSimplifiedAttributeName($name) . " = '" . $value . "'";
    			} else {
    	
    				$condition .= MWebfile::getSimplifiedAttributeName($name) . " IN (";
    	
    				$firstInnerValue = true;
    				foreach ($value as $innerValue) {
    					if ( ! $firstInnerValue ) {
    						$condition .= " , ";
    					}
    					$condition .= '\'' . $innerValue . '\'';
    					$innerValue = false;
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
    private function createMetadataTable() {
    	
    	$table = new MDatabaseTable(
    			$this->databaseConnection,
    			$this->databaseConnection->getTablePrefix() . 'metadata');
    	$table->setIdentifier("id", 10);
    	
    	
    	$table->addColumn(
    				"classname",
    				MIDbDatatypes::varchar(),
    				250);
    	$table->addColumn(
	    			"version",
	    			MIDbDatatypes::int(),
	    			50);
    	$table->addColumn(
	    			"tablename",
	    			MIDbDatatypes::varchar(),
	    			250);
    	
    	$table->create();
    	
    }
    
    private function metadataExist($tablename) {
    	if ( ! $this->tableExistsByTablename($this->databaseConnection->getTablePrefix() . "metadata") ) {
    		$this->createMetadataTable();
    	}
    	$oDatabaseResultSet = $this->databaseConnection->query("SELECT * FROM " . $this->databaseConnection->getTablePrefix() . "metadata WHERE tablename = '" . $tablename . "'" );
    	if ($oDatabaseResultSet->num_rows > 0) {
    		return true;
    	}
    	return false;
    }
    
    private function addMetadata($className, $version, $tablename) {
    	
    	if ( ! $this->tableExistsByTablename($this->databaseConnection->getTablePrefix() . "metadata") ) {
    		$this->createMetadataTable();
    	}
    	$className = str_replace('\\', '\\\\', $className);
    	$this->databaseConnection->query("INSERT INTO " . $this->databaseConnection->getTablePrefix() . "metadata (classname, version, tablename) VALUES ('" . $className . "' , '" . $version . "' , '" . $tablename . "');" );
    }
    
    private function getMetadataForTablename($tablename) {
    	
    	if ( ! $this->tableExistsByTablename($this->databaseConnection->getTablePrefix() . "metadata") ) {
    		$this->createMetadataTable();
    	}
    	
    	$oDatabaseResultSet = $this->databaseConnection->query("SELECT * FROM " . $this->databaseConnection->getTablePrefix() . "metadata WHERE tablename = '" . $tablename . "'" );
    	if ($oDatabaseResultSet->num_rows > 0) {
    		$result = $oDatabaseResultSet->fetch_object();
    	}
    	return $result;
    }
    
    private function getClassnameForTablename($tablename) {
    	
    	if ( ! $this->tableExistsByTablename($this->databaseConnection->getTablePrefix() . "metadata") ) {
    		$this->createMetadataTable();
    	}
    	
    	$metadata = $this->getMetadataForTablename($tablename);
    	return $metadata->classname;
    }
    
}
