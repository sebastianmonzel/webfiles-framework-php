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
 * description
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatabaseDatastore extends MAbstractDatastore 
							implements MISingleDatastore {
	
	private $databaseConnection;
	
	public function tryConnect() {
		return $this->databaseConnection->connect();
	}
	
	public function isReadOnly() {
		return false;
	}
	
	public function getNextWebfileForTime($time) {
		
	}
	
	public function getTime() {
		return NULL;
	}
	
	public function getGeograficPosition() {
		return NULL;
	}
	
	public function __construct(MDatabaseConnection $databaseConnection) {
		$this->databaseConnection = $databaseConnection;
	}
	
	/**
     * Creates a database table to persist objects of this type.
     * @return void
     */
    private function createTable(MWebfile $webfile) {
    	
    	$sAttributeArray = $webfile->getAttributes();
    	
    	$table = new MDatabaseTable(
    						$this->databaseConnection ,
    						$this->getDatabaseTableName($webfile));
    	$table->setIdentifier("id", 10);
    	
    	foreach ( $sAttributeArray as $oAttribute ) {
    		
    		$sAttributeName = $oAttribute->getName();
    		
    		if ( MWebfile::isSimpleDatatype($sAttributeName)
    				&& MWebfile::getSimplifiedAttributeName($sAttributeName) != "id") {
    			
    			// TODO um weitere Typen erweitern
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
    	$table->drop();
    	$table->create();
    	
    }
    
    private function webfileExists(MWebfile $webfile) {
    	
    	$tableName = $this->getDatabaseTableName($webfile);
    	
    	//echo "SELECT * FROM " . $tableName . " WHERE id='" . $webfile->getId() . "'";
    	
    	$query = $this->databaseConnection->query("SELECT * FROM " . $tableName . " WHERE id='" . $webfile->getId() . "'");
    	return ( $query->num_rows > 0 );
    	
    }
    
    private function tableExists(MWebfile $webfile) {
    	
    	$tableName = $this->getDatabaseTableName($webfile);
    	return $this->tableExistsByName($tableName);
    }
    
    private function tableExistsByName($tableName) {
    	 
    	$query = $this->databaseConnection->query("SHOW TABLES FROM `" . $this->databaseConnection->getDatabase() . "`");
    
    	while ( $oDatabaseResultRow = $query->fetch_object() ) {
    
    		$tablesInVariableName = "Tables_in_" . $this->databaseConnection->getDatabase();
    
    		if ( $oDatabaseResultRow->$tablesInVariableName == $tableName ) {
    			return true;
    		}
    		
    	}
    	return false;
    }
    
    
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
    
    public function getWebfilestream() {
    	return new MWebfileStream($this->getWebfilesFromDatastore());
    }
    
    public function addWebfilesFromWebfilestream(MWebfileStream $webfileStream) {
    	$webfiles = $webfileStream->getWebfiles();
    	foreach ($webfiles as $webfile) {
    		$this->storeWebfile($webfile);
    	}
    }
    
	public function getWebfilesFromDatastore() {
		return $this->getByCondition();
	}
	
	/**
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::storeWebfile()
	 * @return Returns the id given in database (in case of a new webfile 
	 * the generated id will be returned)
	 */
	public function storeWebfile(MWebfile $webfile) {
		if ( ! $this->tableExists($webfile) ) {
			$this->createTable($webfile);
		}
		if ( ! $this->webfileExists($webfile) ) {
			return $this->add($webfile);
		} else {
			return $this->update($webfile);
		}
	}
	

	public function getLatestWebfiles($count = 5) {
	
	}
	
	public function getDatasetsFromDatastore() {
	
		$items = $this->getItemsFromDatastore();
	
		$datasets = array();
		if ( $items != null ) {
			foreach ($items as $item) {
				array_push($datasets, $item->getDataset());
			}
			return $datasets;
		} else {
			return null;
		}
	
	}
	
	public function getLatestDatasets($count = 5, $reverse = true) {
	
	}
	
	private function add(MWebfile $webfile, $p_bUseOnlySimpleDatatypes = 0) {
		
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
	        		
					if ( ! $p_bUseOnlySimpleDatatypes ) {
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
	
	private function update(MWebfile $webfile, $p_bUseOnlySimpleDatatypes = 0) {
    	
		$oAttributeArray = $webfile->getAttributes();
    	
        $setValuesString = "";
        $bIsFirstLoop = true;
       
        foreach ($oAttributeArray as $oAttribute) {
        	$oAttribute->setAccessible(true);
        	$sAttributeName = $oAttribute->getName();
        	
        	
        	if ( $sAttributeName != "m_iId" && (
        			MWebfile::isObject($sAttributeName) || 
        			MWebfile::isSimpleDatatype($sAttributeName) ) ) {
	        	
        		if ( ! $bIsFirstLoop ) {
	        		$setValuesString .= ",";
	        	}
	        	$sAttributeDatabaseFieldName = MWebfile::getSimplifiedAttributeName($sAttributeName);
	        	$setValuesString .= $sAttributeDatabaseFieldName;
	        	if (MWebfile::isSimpleDatatype($sAttributeName)) {
		        	$setValuesString .= " = '" . $oAttribute->getValue($webfile) . "'";
	        	} else if (MWebfile::isObject($sAttributeName)) {
	        		
					if ( ! $p_bUseOnlySimpleDatatypes ) {
						if ($this->$sAttributeName->getId() != 0) {
							$this->$sAttributeName->update(1);
							$sAttributeId = $this->$sAttributeName->getId();
						} else {
							$sAttributeId = $this->$sAttributeName->add(1);
						}
						$setValuesString .= "id";
		        		$setValuesString .= " = \"" . $sAttributeId . "\"";
					}
	        	}
	        	if ($bIsFirstLoop)
	        		$bIsFirstLoop = false;
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
     * @param unknown_type $p_sClassName
     */
    public function getDatabaseTableName($webfile) {
   		
    	$classname = $webfile::$m__sClassName;
    	
    	if ( strpos($classname, "\\") != -1 ) { // check if classname is given with namespace
    		
    		$lastBackslashOccurrence = strrpos($classname, "\\");
    		$classname = substr($classname, $lastBackslashOccurrence+1);
    	} 
    	
   		$tableName = $this->databaseConnection->getTablePrefix() . $classname;
    	return $tableName;
    }
    
    public function getClassNameFromTableName($tableName) {
    	$tablePrefix = $this->databaseConnection->getTablePrefix();
    	$metadata = $this->getMetadataForTablename($tableName);
    	return $metadata->classname;
    	//return substr($tableName, strlen($tablePrefix));
    }
	
    /**
     * Returns a set of webfiles added by a template.
     * not setted attributes will be filled by a ?
     * @param unknown_type $webfile
     */
    public function getByTemplate(MWebfile $webfile) {
    	
    	$webfileArray = array();
    	
    	if ( $this->tableExists($webfile) ) {
    	
	    	// determine table with webfile type
	    	$tableName = $this->getDatabaseTableName($webfile);
	    	
	    	// translate template into a condition
	    	
	    	$first = true;
	    	$condition = "";
	    	
	    	$attributes = $webfile->getAttributes(true);
	    	
	    	foreach ($attributes as $attribute) {
	    		
	    		$attribute->setAccessible(true);
	    		
	    		$name  = $attribute->getName();
	    		$value = $attribute->getValue($webfile);
	    		
	    		if ( $value != "?" && ! ($value instanceof MIDatastoreFunction) ) {
		    		if ( ! $first ) {    			
		    			$condition .= " AND ";
		    		}
		    		
		    		if ( is_array($value) ) {
		    			$condition .= MWebfile::getSimplifiedAttributeName($name) . " IN (";
		    			 
		    			$firstInnerValue = true;
		    			foreach ($value as $innerValue) {
		    				if ( ! $firstInnerValue ) {
		    					$condition .= " , ";
		    				}
		    				$condition .= '\'' . $innerValue . '\'';
		    				$firstInnerValue = false;
		    			}
		    			$condition .= ')';
		    		} else if ( $value instanceof MTimespan ) {
		    			$condition .= MWebfile::getSimplifiedAttributeName($name) . " BETWEEN '" . $value->getStart() . "' AND '" . $value->getEnd() . "'";
		    		} else if ( $value instanceof MSubstringFiltering ) {
		    			
		    			$condition .= "LOWER(" . MWebfile::getSimplifiedAttributeName($name) . ") LIKE LOWER('" . $value->getValue() . "%')";
		    		} else {
		    			$condition .= MWebfile::getSimplifiedAttributeName($name) . " = '" . $value . "'";
		    		}
	    			$first = false;
	    		}
	    	}
	    	
	    	$first = true;
	    	$order = "";
	    	
	    	
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
				    while ( $oDatabaseResultRow = $oDatabaseResultSet->fetch_object() ) {
			    		
			    		$className = $this->getClassNameFromTableName($tableName);
			    		
			    		$webfile = new $className();
			    		foreach ($attributes as $oAttribute) {
			    			
			    			$oAttribute->setAccessible(true);
			    			
			    			$sAttributeName = $oAttribute->getName();
			    			if (MWebfile::isSimpleDatatype($sAttributeName)) {
				    			$sDatabaseFieldName = MWebfile::getSimplifiedAttributeName($sAttributeName);
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
		    	}
	    	}
    	}
    	
    	return $webfileArray;
    	
    }
    
    /**
     * Fetches all representatives of persistent objects of this type matching the given condition
     * @param $p_sCondition condition to fetch objects
     * @return all representatives of persitent objects matching the given condition
     */
    public function getByCondition($p_sCondition = "") {
    	
    	$tableNames = $this->getAllTableNames();
    	
    	// TODO replace name with name derived from table name
        $sAttributeArray = MBlogEntry::getAttributes();
        
    	$sSqlSelectFields = "";
    	$sSqlJoins = "";
    	
    	$bIsFirst = 1;
    	foreach ( $tableNames as $tableName ) {
    		$className = $this->getClassNameFromTableName($tableName);
    		
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
	    			
	    			//var_export(MBlogEntry::$s__oAggregation);
	    			
	    			//var_export(MBlogEntry::$s__oAggregation[$sAttributeName]);
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

    	if ( $p_sCondition != "" ) {
    		$sSqlQuery .= " WHERE " . $p_sCondition;
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
    }
    
    public function deleteByTemplate(MWebfile $webfile) {
    	$webfileArray = array();
    	 
    	if ( $this->tableExists($webfile) ) {
    		 
    		// determine table with webfile type
    		$tableName = $this->getDatabaseTableName($webfile);
    	
    		// translate template into a condition
    	
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
    	
    	
    	
    		$query = "DELETE FROM " . $tableName;
    	
    		if ( !empty($condition) ) {
    			$query .= " WHERE " . $condition;
    		}
    		
    		$this->databaseConnection->query($query);
    	}
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
    	if ( ! $this->tableExistsByName($this->databaseConnection->getTablePrefix() . "metadata") ) {
    		$this->createMetadataTable();
    	}
    	$oDatabaseResultSet = $this->databaseConnection->query("SELECT * FROM " . $this->databaseConnection->getTablePrefix() . "metadata WHERE tablename = '" . $tablename . "'" );
    	if ($oDatabaseResultSet->num_rows > 0) {
    		return true;
    	}
    	return false;
    }
    
    private function addMetadata($className, $version, $tablename) {
    	
    	if ( ! $this->tableExistsByName($this->databaseConnection->getTablePrefix() . "metadata") ) {
    		$this->createMetadataTable();
    	}
    	$className = str_replace('\\', '\\\\', $className);
    	$this->databaseConnection->query("INSERT INTO " . $this->databaseConnection->getTablePrefix() . "metadata (classname, version, tablename) VALUES ('" . $className . "' , '" . $version . "' , '" . $tablename . "');" );
    }
    
    private function getMetadataForTablename($tablename) {
    	
    	if ( ! $this->tableExistsByName($this->databaseConnection->getTablePrefix() . "metadata") ) {
    		$this->createMetadataTable();
    	}
    	
    	$oDatabaseResultSet = $this->databaseConnection->query("SELECT * FROM " . $this->databaseConnection->getTablePrefix() . "metadata WHERE tablename = '" . $tablename . "'" );
    	if ($oDatabaseResultSet->num_rows > 0) {
    		$result = $oDatabaseResultSet->fetch_object();
    	}
    	return $result;
    }
    
    private function getClassnameForTablename($tablename) {
    	
    	if ( ! $this->tableExistsByName($this->databaseConnection->getTablePrefix() . "metadata") ) {
    		$this->createMetadataTable();
    	}
    	
    	$metadata = $this->getMetadataForTablename($tablename);
    	return $metadata->classname;
    }
    
}
