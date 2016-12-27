<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format;

/**
 * Base cass for all webfile class definitions.<br />
 * On the following <a href="http://simpleserv.de/webfiles/doc/doku.php?id=definitionwebfile">link</a>
 * you can find more information about the definition of webfiles.
 *
 * @author     simpleserv company < info@simpleserv.de >
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
     * @var string
     */
    public static $m__sClassName;

    /**
     * Converts the current webfile into its xml representation.
     *
     * @param boolean $usePreamble sets the option of using a preamble in xml - usually used for setting the version of xml an the encoding.
     * @return string returns the webfile as a marshalled String.
     */
    public function marshall($usePreamble = true)
    {
        $out = "";
        $attributes = $this->getAttributes();
        if ($usePreamble) {
            $out .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        }
        $out .= "<object classname=\"" . static::$m__sClassName . "\">\n";
        foreach ($attributes as $attribute) {

            $attributeName = $attribute->getName();

            if (MWebfile::isSimpleDatatype($attributeName)) {
                $attribute->setAccessible(true);
                $attributeFieldName = static::getSimplifiedAttributeName($attributeName);
                $out .= "\t<" . $attributeFieldName . "><![CDATA[" . $attribute->getValue($this) . "]]></" . $attributeFieldName . ">\n";
            }
        }
        $out .= "</object>";

        return $out;
    }

    /**
     * Converts the given xml into a webfile object.
     * @param string $data xml which represents a webfile.
     */
    public function unmarshall($data)
    {
        $root = simplexml_load_string($data);

        if ($root->getName() == "reference") {
            $url = $root->url;
            $data = file_get_contents($url);

            $root = simplexml_load_string($data);
        }

        if ($root != null) {

            $objectAttributes = $root->children();
            $attributes = $this->getAttributes();

            /** @var \SimpleXMLElement $value */
            foreach ($objectAttributes as $value) {

                foreach ($attributes as $attribute) {
                    $attributeName = $attribute->getName();
                    $attribute->setAccessible(true);

                    if ($value->getName() == static::getSimplifiedAttributeName($attributeName)) {
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
     * Converts the given xml-String into a webfile object.
     * @param string $xmlAsString
     * @return MWebfile
     */
    public static function staticUnmarshall($xmlAsString)
    {
        $root = simplexml_load_string($xmlAsString);

        if ($root->getName() == "reference") {
            $url = $root->url;
            $xmlAsString = file_get_contents($url);

            $root = simplexml_load_string($xmlAsString);
        }

        if ($root != null) {

            $classname = (string)$root->attributes()->classname;

            // INSTANCIATE NEW
            $ref = new \ReflectionClass($classname);
            $item = $ref->newInstanceWithoutConstructor();

            // OLD VERSION
            //$item = new $classname();

            $objectAttributes = $root->children();
            $attributes = $item->getAttributes();

            /** @var \SimpleXMLElement $value */
            foreach ($objectAttributes as $value) {
                /** @var \ReflectionProperty $attribute */
                foreach ($attributes as $attribute) {

                    $attribute->setAccessible(true);
                    $attributeName = $attribute->getName();
                    if ($value->getName() == static::getSimplifiedAttributeName($attributeName)) {
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
     * In case of using the current webfile object for making a request
     * on a datastore (getByTemplate()) this method helps to
     * set the defaults for making the template request.
     */
    public function presetForTemplateSearch()
    {
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute) {

            $attributeName = $attribute->getName();

            if (MWebfile::isSimpleDatatype($attributeName)) {
                $attribute->setAccessible(true);
                $attribute->setValue($this, "?");
            }
        }
    }

    public function matchesTemplate(MWebfile $template) {

        if ( $template::$m__sClassName == static::$m__sClassName ) {

            $attributes = $template->getAttributes(true);

            /** @var \ReflectionProperty $attribute */
            foreach ($attributes as $attribute) {

                $attribute->setAccessible(true);
                $templateValue = $attribute->getValue($template);

                if (
                    $templateValue != "?"
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
     * Returns the attributes of the actual class which are relevant for the
     * webfile definition.
     *
     * @param bool $onlyAttributesOfSimpleDatatypes
     * @return array array with attributes
     */
    public static function getAttributes($onlyAttributesOfSimpleDatatypes = false)
    {
        $oSelfReflection = new \ReflectionClass(static::$m__sClassName);
        $oPropertyArray = $oSelfReflection->getProperties(
            \ReflectionProperty::IS_PUBLIC |
            \ReflectionProperty::IS_PROTECTED |
            \ReflectionProperty::IS_PRIVATE);

        $count = 0;
        while ($count < count($oPropertyArray)) {
            $sAttributeName = $oPropertyArray[$count]->getName();
            if (
                // TODO generalize attribute prefix (sample "m_-s-", (start 2, length 1) )
                substr($sAttributeName, 1, 1) != "_" ||
                substr($sAttributeName, 2, 1) == "_" ||
                ($onlyAttributesOfSimpleDatatypes && substr($sAttributeName, 2, 1) == "o")
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
     */
    public static function getClassInformation()
    {

        $returnValue = "<classinformation>\n";
        $returnValue .= "\t<author>simpleserv.de</author>\n";
        $returnValue .= "\t<classname>" . static::$m__sClassName . "</classname>\n";
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
     * @return array
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
     * @param $time unix timestamp of the context time.
     */
    public function setTime($time)
    {
        $this->m_iTime = $time;
    }

    public function getGeograficPosition()
    {
        return NULL;
    }

}