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
 * Base cass for webfile class definitions.
 *
 * @package    de.simpleserv
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <mail@sebastianmonzel.de>
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
	
	/**
	 * 
	 * @param timestamp $time
	 */
	public function setTime($time) {
		$this->time = $time;
	}
		
	public function getGeograficPosition() {
		return NULL;
	}
	
	/**
	 * Converts the current webfile into its xml representation.
	 * 
	 * @param boolean $usePreamble
	 * @return string returns the webfile as String.
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
    
    /**
     * In case of using the current webfile object for making a request
     * on a datastore (getByTemplate()) this method helps to
     * set the defaults for making the template request.
     */
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
     * Converts the given xml into a webfile object.
     * 
     * @param string $data xml which represents a webfile.
     * @return MWebfile converted webfile
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
     * returns true if attribute is a simple datatype (for example
     * string, integer or boolean).
     *
     * @param string $datatypeName
     * @return boolean
     */
    public static function isSimpleDatatype($datatypeName)
    {
    	if ( ! self::isObject($datatypeName) && substr($datatypeName,2,1) != "_" ) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * returns true if attribute is object in the other case the returnvalue is false
     * @param string $attributeName
     * @return boolean
     */
    public static function isObject($attributeName)
    {
    	if ( substr($attributeName,2,1) == "o" ) {
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
    	 
    	$attributes = $this->getAttributes();
    
    	foreach ($attributes as $attribute) {
    		$attributeName = $attribute->getName();
    		$attribute->setAccessible(true);
    		$attributeValue = $attribute->getValue($this);
    		if ( MWebfile::isSimpleDatatype($attributeName) ) {
    			$attributeFieldName = static::getSimplifiedAttributeName($attributeName);
    			$dataset[$attributeFieldName] = $attribute->getValue($this);
    		}
    	}
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