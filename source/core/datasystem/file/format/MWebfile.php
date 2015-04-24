<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format;

use simpleserv\webfilesframework\MItem;

/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 */

/**
 * description
 *
 * @package    de.simpleserv
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MWebfile extends MItem {
	
	protected $time;
	
	/**
	 * 
	 */
	public function getTime() {
		return $this->time;
	}
	
	public function setTime($time) {
		$this->time = $time;
	}
		
	public function getGeograficPosition() {
		return NULL;
	}
	
	/**
     * 
     * Enter description here ...
     */
    public function marshall($usePreamble = true) {
    	$out = "";
		$attributes = $this->getAttributes();
		if ( $usePreamble ) {
			$out .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		}
		$out .= "<object classname=\"" . static::$m__sClassName ."\">\n";
		foreach ($attributes as $attribute) {
			
			$attributeName = $attribute->getName();
			
			if ( MWebfile::isSimpleDatatype($attributeName) ) {
				$attribute->setAccessible(true);
				$attributeFieldName = static::getSimplifiedAttributeName($attributeName);				
				$out .= "\t<" . $attributeFieldName . "><![CDATA[" . $attribute->getValue($this) . "]]></" . $attributeFieldName . ">\n";
			}
		}
		$out .= "</object>";
				
		return $out;
    }
    
    public function presetDefaultForTemplate() {
    	$attributes = $this->getAttributes();
    	foreach ($attributes as $attribute) {
    		
    		$attributeName = $attribute->getName();
    		
    		if ( MWebfile::isSimpleDatatype($attributeName) ) {
    			$attribute->setAccessible(true);
    			$attribute->setValue($this,"?");
    		}
    	}
    
    }
	
	
	/**
     * 
     * Enter description here ...
     * @param $data
     */
    public function unmarshall($data) {
    	
    	$root = simplexml_load_string($data);
    	
   		if ( $root->getName() == "reference" ) {
    		$url = $root->url;
    		$data = file_get_contents($url);
    		
    		$root = simplexml_load_string($data);
    	}
    	
    	if ( $root != null ) {
		    
    		$objectAttributes = $root->children();
		    $attributes = $this->getAttributes();
		    
		    foreach ( $objectAttributes as $value ) {
		    	
		    	foreach ($attributes as $attribute) {
		    		$attributeName = $attribute->getName();
		 			$attribute->setAccessible(true);
		 			
		    		if ( $value->getName() == static::getSimplifiedAttributeName($attributeName) ) {		    			
		    			$attribute->setValue($this, $value->__toString());
		    		}
		    	}
		    }
    	} else {
    		echo("Fehler beim Lesen des XML");
    	}
		
    }
    
    
	/**
     * 
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function staticUnmarshall($data) {
    	$root = simplexml_load_string($data);
    	
    	if ( $root->getName() == "reference" ) {
    		$url = $root->url;
    		$data = file_get_contents($url);
    		
    		$root = simplexml_load_string($data);
    	}
    	
    	if ( $root != null ) {
    		
    		$classname = (string)$root->attributes()->classname;
    		
    		// INSTANITE NEW 
    		$ref = new \ReflectionClass($classname);
    		$item = $ref->newInstanceWithoutConstructor();
    		
    		// OLD VERSION
    		//$item = new $classname();
    		
		    $objectAttributes = $root->children();
		    $attributes = $item->getAttributes();
		    
		    foreach ( $objectAttributes as $value ) {
		    	
		    	foreach ($attributes as $attribute) {
		    		
		    		$attribute->setAccessible(true);
		 			$attributeName = $attribute->getName();
		    		if ( $value->getName() == static::getSimplifiedAttributeName($attributeName) ) {
		    			$attribute->setValue($item, $value->__toString());
		    		}
		    	}
		    	
		    }
		    return $item;
    	} else {
    		echo("Fehler beim Lesen des XML");
    		return null;
    	}
    }
    
    /**
     * returns all variables with name and value with prefix CONTENT_VAR_PREFIX
     * in an array.
     * @return array with variables
     */
    public function getContentVars() {
    	$classVars = get_class_vars(get_class($this));
    	$contentVars = array();
    	foreach ( $classVars as $classVarName => $classVarValue ) {
    		if ( substr($classVarName,0,strlen(CONTENT_VAR_PREFIX)) == CONTENT_VAR_PREFIX ) {
    			$contentVarValue = substr($contentVarValue,strlen(CONTENT_VAR_PREFIX));
    			array_push($contentVars,$classVarValue);
    		}
    	}
    	return $contentVars;
    }
    
    /**
     * @todo write description
     * @param MString $p_sName
     * @return <type>
     */
    public function getSerializedValueByVariableName($p_sName) {
    
    	$contentVarName = CONTENT_VAR_PREFIX . $p_sName;
    	$contentVarValue = $$contentVarName;
    	$serialized_content_var_value = serialize($this->$content_var_value);
    
    	return $serialized_content_value;
    
    }
    
    /**
     * setsTheValue of an contentValue with begins with the prefix
     * CONTENT_VAR_PREFIX.
     * @param <type> $name
     * @param <type> $serializedContentVarValue
     */
    public function setSerializedValueByVariableName($name, $serializedContentVarValue ) {
    	$$name = unserialize($serializedContentVarValue);
    }
    
    
    /**
     * unique id of the item
     * mapped to every item as "id" in the database
     *
     * @var int
     */
    
    
    /**
     * returns true if attribute is simple datatype as string, integer
     * or boolean.
     *
     * @param unknown_type $p_sDatatypeName
     * @return unknown
     */
    public static function isSimpleDatatype($p_sDatatypeName)
    {
    	if ( ! self::isObject($p_sDatatypeName) && substr($p_sDatatypeName,2,1) != "_" ) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * returns true if attribute is object in the other case the returnvalue is false
     * @param attribute $p_sDatatypeName
     * @return boolean
     */
    public static function isObject($p_sAttributeName)
    {
    	if ( substr($p_sAttributeName,2,1) == "o" ) {
    		return true;
    	} else {
    		return false;
    	}
    
    }
    
    /**
     * returns attributes of the actual class and the extended classes<br />
     * @return array with attributes
     */
    public static function getAttributes($onlyAttributesOfSimpleDatatypes = false) {
    	$oSelfReflection = new \ReflectionClass(static::$m__sClassName);
    	$oPropertyArray = $oSelfReflection->getProperties(
    			\ReflectionProperty::IS_PUBLIC |
    			\ReflectionProperty::IS_PROTECTED |
    			\ReflectionProperty::IS_PRIVATE);
    	 
    	$count = 0;
    	while ( $count < count($oPropertyArray) ) {
    		$sAttributeName = $oPropertyArray[$count]->getName();
    		if  (
    		substr($sAttributeName,1,1) != "_" ||
    		substr($sAttributeName,2,1) == "_" ||
    		( $onlyAttributesOfSimpleDatatypes && substr($sAttributeName,2,1) == "o" )
    		) {
    			unset($oPropertyArray[$count]);
    		}
    		$count++;
    	}
    	return $oPropertyArray;
    }
    
    /**
     *
     * Returns a xml defined class information. It cotains classname
     */
    public static function getClassInformation() {
    	 
    	$returnValue  = "<classinformation>\n";
    	$returnValue .= "\t<author>simpleserv.de</author>\n";
    	$returnValue .= "\t<classname>" . static::$m__sClassName . "</classname>\n";
    	$returnValue .= "\t<attributes>\n";
    	 
    	$attributes = static::getAttributes();
    
    	foreach ($attributes as $attribute) {
    			
    		$attributeName = $attribute->getName();
    			
    		if ( MWebfile::isSimpleDatatype($attributeName) ) {
    			$attributeFieldName = static::getSimplifiedAttributeName($attributeName);
    			$attributeFieldType = MItem::getDatatypeFromAttributeName($attributeName);
    			$returnValue .= "\t\t<attribute name=\"" . $attributeFieldName . "\" type=\"" . $attributeFieldType . "\" />\n";
    		}
    			
    			
    	}
    	$returnValue .= "\t</attributes>\n";
    	$returnValue .= "</classinformation>";
    	return $returnValue;
    	 
    }
    
    /**
     *
     * Enter description here ...
     * @param unknown_type $attributeName
     */
    public static function getDatatypeFromAttributeName($attributeName) {
    	 
    	$token = substr($attributeName, 2,1);
    	if ( $token == "s" ) {
    		return "shorttext";
    	} else if ( $token == "l" ) {
    		return "longtext";
    	} else if ( $token == "h" ) {
    		return "htmllongtext";
    	} else if ( $token == "d" ) {
    		return "date";
    	} else if ( $token == "t" ) {
    		return "time";
    	} else if ( $token == "i" ) {
    		return "integer";
    	} else if ( $token == "f" ) {
    		return "float";
    	} else if ( $token == "o" ) {
    		return "object";
    	}
    	return null;
    }
    
    /**
     *
     * Enter description here ...
     */
    public function getDataset() {
    	$dataset = array();
    	 
    	//var_dump($this);
    	 
    	$attributes = $this->getAttributes();
    
    	//var_dump($attributes);
    
    	foreach ($attributes as $attribute) {
    		$attributeName = $attribute->getName();
    		$attribute->setAccessible(true);
    		$attributeValue = $attribute->getValue($this);
    		if ( MWebfile::isSimpleDatatype($attributeName) ) {
    			$attributeFieldName = static::getSimplifiedAttributeName($attributeName);
    			$dataset[$attributeFieldName] = $attribute->getValue($this);
    		}
    	}
    	//var_dump($dataset);
    	return $dataset;
    }
    
    
    
    /**
     * Returns database field name to a given attribute
     *
     * @param unknown_type $p_sFieldName
     * @return unknown
     */
    public static function getSimplifiedAttributeName($p_sFieldName) {
    	$sDatabaseFieldName = substr($p_sFieldName,3);
    	$sDatabaseFieldName = strtolower($sDatabaseFieldName);
    	return $sDatabaseFieldName;
    }
    
}