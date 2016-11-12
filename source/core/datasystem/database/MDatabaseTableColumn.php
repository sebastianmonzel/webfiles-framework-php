<?php

namespace simpleserv\webfilesframework\core\datasystem\database;
use simpleserv\webfilesframework\MWebfilesFrameworkException;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatabaseTableColumn
{

    var $name;
    var $type;
    var $length;

    /**
     * @param string $name
     * @param string $type
     * @param int $length
     */
    public function __construct($name, $type, $length = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->length = $length;
    }

    /**
     * @return string
     * @throws MWebfilesFrameworkException
     */
    public function getStringRepresentation()
    {

        if ($this->type == MDatabaseDatatypes::VARCHAR) {
            return "`" . $this->name . "` varchar(" . $this->length . ") CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,";
        } elseif ($this->type == MDatabaseDatatypes::TEXT) {
            return "`" . $this->name . "` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,";
        } elseif ($this->type == MDatabaseDatatypes::INT) {
            return "`" . $this->name . "` int(" . $this->length . ") NOT NULL,";
        } else {
            throw new MWebfilesFrameworkException("Unknown Datatype: " + $this->type);
        }
    }

}
