<?php

namespace webfilesframework\core\datasystem\file\format;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use SimpleXMLElement;
use webfilesframework\core\datastore\functions\MIDatastoreFunction;
use webfilesframework\MWebfilesFrameworkException;

/**
 * Base class for all webfile class definitions.<br />
 * On the following <a href="http://sebastianmonzel.github.io/webfiles-framework-doc/webf/webf_01_defintion.html">link</a>
 * you can find more information about the definition of webfiles.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWebfile {

    protected $m_iId = 0;

    /**
     * @var int sets the time of the main context of the given webfile.
     * Example:<br />
     * An event would have the point when it takes place. An news entry
     * would have the creation time as context time.
     */
    public $m_iTime;

    /**
     * Converts the current webfile into its xml representation.
     *
     * @param bool $usePreamble sets the option of using a preamble in xml - usually used for setting the version of xml an the encoding.
     *
     * @param bool $marshallAsJSON
     * @return string string returns the webfile as a marshalled String.
     * @throws ReflectionException
     */
    public function marshall($usePreamble = true, $marshallAsJSON = false)
    {
        if ( ! $marshallAsJSON ) {
            return $this->marshalAsXml($usePreamble);
        } else {
            return $this->marshalAsJson();
        }
    }

	/**
	 * Converts the given xml into a webfile object.
	 *
	 * @param string $data xml which represents a webfile.
	 *
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public function unmarshall($data)
    {
        static::genericUnmarshall($data,$this);
    }

	/**
	 * Converts the given xml-String into a new webfile object.
	 *
	 * @param string $xmlAsString
	 *
	 * @return MWebfile|object
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
    public static function staticUnmarshall($xmlAsString)
    {
        return static::genericUnmarshall($xmlAsString);
    }


	/**
	 * @param      $xmlAsString
	 * @param null $targetObject
	 *
	 * @return MWebfile|object
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
	private static function genericUnmarshall($input, &$targetObject = null) {
        if ( is_array($input) ) {
            // json already parsed
            return self::genericJsonUnmarshal($input, $targetObject);
        } else if (substr(trim($input),0,1) == "{" ) {
            // json as text
            return self::genericJsonUnmarshal($input, $targetObject);
        } else {
            // xml as text
            return self::genericXmlUnmarshal($input, $targetObject);
        }
    }

    /**
     * @param $xmlAsString
     * @param $targetObject
     * @return object
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    private static function genericXmlUnmarshal($xmlAsString, $targetObject): object
    {
        $root = simplexml_load_string($xmlAsString);

        if ($root == null) {
            throw new MWebfilesFrameworkException("Error on reading initial xml: " . $xmlAsString);
        }

        if ($root->getName() == "reference") {
            $url = $root->url;
            $xmlAsString = file_get_contents($url);
            $root = simplexml_load_string($xmlAsString);

            if ($root == null) {
                throw new MWebfilesFrameworkException("Error on reading reference xml: " . $xmlAsString);
            }
        }

        if ($targetObject == null) {
            $classname = (string)$root->attributes()->classname;
            $targetObject = MWebfile::createWebfileByClassname($classname);
        }

        $objectAttributes = $root->children();
        $attributes = $targetObject->getAttributes();

        /** @var SimpleXMLElement $value */
        foreach ($objectAttributes as $value) {
            /** @var ReflectionProperty $attribute */
            foreach ($attributes as $attribute) {

                $attribute->setAccessible(true);
                $attributeName = $attribute->getName();
                if ($value->getName() == static::getSimplifiedAttributeName($attributeName)) {
                    $attribute->setValue($targetObject, $value->__toString());
                }
            }
        }
        return $targetObject;
    }

    /**
     * @param $input
     * @param $targetObject
     * @return object
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    private static function genericJsonUnmarshal($input, $targetObject): object
    {

        if ( is_array($input) ) {
            // already parsed json
            $jsonRoot = $input;
        } else {
            // json string
            $jsonRoot = json_decode($input, true);
        }

        if ($jsonRoot == null) {
            throw new MWebfilesFrameworkException("Error on reading initial json: " . $input);
        }

        if ($targetObject == null) {
            $classname = (string)$jsonRoot['classname'];
            $targetObject = MWebfile::createWebfileByClassname($classname);
        }

        $objectAttributes = $jsonRoot['webfile'];
        $attributes = $targetObject->getAttributes();

        foreach ($objectAttributes as $key => $value) {
            /** @var ReflectionProperty $attribute */
            foreach ($attributes as $attribute) {

                $attribute->setAccessible(true);
                $attributeName = $attribute->getName();
                if ($key == static::getSimplifiedAttributeName($attributeName)) {
                    $attribute->setValue($targetObject, $value);
                }
            }
        }
        return $targetObject;
    }

	/**
	 * In case of using the current webfile object for making a request
	 * on a datastore (getByTemplate()) this method helps to
	 * set the defaults for making the template request.
	 * @throws ReflectionException
	 */
    public function presetForTemplateSearch()
    {
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute) {

            $attributeName = $attribute->getName();

            if (MWebfile::isSimpleDatatype($attributeName)) {
                $attribute->setAccessible(true);
                $attribute->setValue($this, "[any]");
            }
        }
    }

	/**
	 * @param MWebfile $template
	 *
	 * @return bool
	 * @throws ReflectionException
	 */
	public function matchesTemplate(MWebfile $template) {

        if ( $template::classname() == static::classname() ) {

            $attributes = $template->getAttributes(true);

            /** @var ReflectionProperty $attribute */
            foreach ($attributes as $attribute) {

                $attribute->setAccessible(true);
                $templateValue = $attribute->getValue($template);

                if (
                    $templateValue != "[any]"
                    && !($templateValue instanceof MIDatastoreFunction)
                ) {

                    $webfileValue = $attribute->getValue($this);
                    if ($templateValue != $webfileValue) {
                        return false;
                    }
                }
            }
            return true;
        } else {
            return false;
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
        if (!self::isObject($datatypeName) && substr($datatypeName, 2, 1) != "_") {
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
        if (substr($attributeName, 2, 1) == "o") {
            return true;
        } else {
            return false;
        }

    }


	/**
	 * eturns the attributes of the actual class which are relevant for the
	 * webfile definition.
	 *
	 * @param bool $simpleDatatypesOnly
	 *
	 * @return ReflectionProperty[]
	 * @throws ReflectionException
	 */
    public static function getAttributes($simpleDatatypesOnly = false)
    {
        $oSelfReflection = new ReflectionClass(static::classname());
        $oPropertyArray = $oSelfReflection->getProperties(
            ReflectionProperty::IS_PUBLIC |
            ReflectionProperty::IS_PROTECTED |
            ReflectionProperty::IS_PRIVATE);

        $count = 0;
        while ($count < count($oPropertyArray)) {
            $sAttributeName = $oPropertyArray[$count]->getName();
            if (
                // TODO generalize attribute prefix (sample "m_-s-", (start 2, length 1) )
                substr($sAttributeName, 1, 1) != "_" ||
                substr($sAttributeName, 2, 1) == "_" ||
                ( $simpleDatatypesOnly && substr($sAttributeName, 2, 1) == "o")
            ) {
                unset($oPropertyArray[$count]);
            }
            $count++;
        }
        return $oPropertyArray;
    }

	/**
	 * Returns a xml defined class information. It contains the
	 * classname and the given attributes.
	 *
	 * @return string xml with information about the class
	 * @throws ReflectionException
	 */
    public static function getClassInformation()
    {

        $returnValue = "<classinformation>\n";
        $returnValue .= "\t<author>simpleserv.de</author>\n";
        $returnValue .= "\t<classname>" . static::classname() . "</classname>\n";
        $returnValue .= "\t<attributes>\n";

        $attributes = static::getAttributes();

        foreach ($attributes as $attribute) {

            $attributeName = $attribute->getName();

            if (MWebfile::isSimpleDatatype($attributeName)) {
                $attributeFieldName = static::getSimplifiedAttributeName($attributeName);
                $attributeFieldType = MWebfile::getDatatypeFromAttributeName($attributeName);
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
     * @param string $attributeName
     * @return null|string
     */
    public static function getDatatypeFromAttributeName($attributeName)
    {
        // TODO generalize attribute prefix (sample "m_-s-", (start 2, length 1) )
        $datatypeToken = substr($attributeName, 2, 1);

        if ($datatypeToken == "s") {
            return "shorttext";
        } else if ($datatypeToken == "l") {
            return "longtext";
        } else if ($datatypeToken == "h") {
            return "htmllongtext";
        } else if ($datatypeToken == "d") {
            return "date";
        } else if ($datatypeToken == "t") {
            return "time";
        } else if ($datatypeToken == "i") {
            return "integer";
        } else if ($datatypeToken == "f") {
            return "float";
        } else if ($datatypeToken == "o") {
            return "object";
        }
        return null;
    }

	/**
	 * Transforms the actual webfile into an dataset. A dataset is represented by a key value array.
	 * The key is the attributes name. The value is the attributes value.
	 *
	 * @return array
	 * @throws ReflectionException
	 */
    public function getDataset()
    {
        $dataset = array();

        $attributes = $this->getAttributes();

        foreach ($attributes as $attribute) {

            $attributeName = $attribute->getName();
            $attribute->setAccessible(true);
            $attributeValue = $attribute->getValue($this);

            if (MWebfile::isSimpleDatatype($attributeName)) {
                $attributeFieldName = static::getSimplifiedAttributeName($attributeName);
                $dataset[$attributeFieldName] = $attributeValue;
            }
        }
        return $dataset;
    }


    /**
     * Returns database field name to a given attribute
     *
     * @param string $p_sFieldName
     * @return string
     */
    public static function getSimplifiedAttributeName($p_sFieldName)
    {
        // TODO generalize attribute prefix (sample "m_-s-", (start 2, length 1) )
        $sDatabaseFieldName = substr($p_sFieldName, 3);
        $sDatabaseFieldName = strtolower($sDatabaseFieldName);
        return $sDatabaseFieldName;
    }

    public function getId()
    {
        return $this->m_iId;
    }

    public function setId($itemId)
    {
        $this->m_iId = $itemId;
    }

    /**
     *
     */
    public function getTime()
    {
        return $this->m_iTime;
    }

    /**
     * @param $time int unix timestamp of the context time.
     */
    public function setTime($time)
    {
        $this->m_iTime = $time;
    }

    public function getGeograficPosition()
    {
        return NULL;
    }

    public static function classname() {
    	return get_called_class();
    }

	/**
	 * @param $classname
	 *
	 * @return MWebfile
	 * @throws MWebfilesFrameworkException
	 * @throws ReflectionException
	 */
	public static function createWebfileByClassname($classname) {
		$reflectionClass = new ReflectionClass($classname);
		$webfile = $reflectionClass->newInstanceWithoutConstructor();
		if (! $webfile instanceof MWebfile ) {
			throw new MWebfilesFrameworkException("given class '" . $classname . " does not extend MWebfile.");
		}
		return $webfile;
	}

    /**
     * @param bool $usePreamble
     * @return string
     * @throws ReflectionException
     */
    public function marshalAsXml(bool $usePreamble): string
    {
        $xml = "";
        $attributes = $this->getAttributes();
        if ($usePreamble) {
            $xml .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        }
        $xml .= "<object classname=\"" . static::classname() . "\">\n";
        foreach ($attributes as $attribute) {

            $attributeName = $attribute->getName();

            if (MWebfile::isSimpleDatatype($attributeName)) {
                $attribute->setAccessible(true);
                $attributeFieldName = static::getSimplifiedAttributeName($attributeName);
                $xml .= "\t<" . $attributeFieldName . "><![CDATA[" . $attribute->getValue($this) . "]]></" . $attributeFieldName . ">\n";
            }
        }
        $xml .= "</object>";

        return $xml;
    }

    /**
     * @param bool $usePreamble
     * @return string
     * @throws ReflectionException
     */
    public function marshalAsJson(): string
    {
        $json = "";
        $attributes = $this->getAttributes();
        $json .= "{\n";
        $json .= "\t\"classname\": \"" . str_replace("\\", "\\\\", static::classname()) . "\",\n";
        $json .= "\t\"webfile\": {\n";


        foreach ($attributes as $key => $attribute) {

            $attributeName = $attribute->getName();

            if (MWebfile::isSimpleDatatype($attributeName)) {
                $attribute->setAccessible(true);
                $attributeFieldName = static::getSimplifiedAttributeName($attributeName);
                $json .= "\t\t\"" . $attributeFieldName . "\": \"" . $attribute->getValue($this) . "\"";
                if (next($attributes)==true) $json .= ",";
                $json .= "\n";
            }
        }
        $json .= "\t}";
        $json .= "}";

        return $json;
    }

}