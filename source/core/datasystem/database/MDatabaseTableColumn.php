<?php

namespace simpleserv\webfilesframework\core\datasystem\database;

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

    public function __construct($name, $type, $length = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->length = $length;
    }

    public function getStringRepresentation()
    {

        if ($this->type == "varchar") {
            return "`" . $this->name . "` varchar(" . $this->length . ") CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,";
        } elseif ($this->type == "text") {
            return "`" . $this->name . "` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,";
        } elseif ($this->type == "int") {
            return "`" . $this->name . "` int(" . $this->length . ") NOT NULL,";
        }

    }

}
