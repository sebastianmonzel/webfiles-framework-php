<?php

namespace simpleserv\webfilesframework\io\form\webfile;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Handles requests which are submitted through a form generated
 * by MWebfileFormVisualizer.
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWebfileFormHandler
{

    var $requestArray;

    /**
     * @param $requestArray
     */
    public function __construct($requestArray)
    {
        $this->requestArray = $requestArray;
    }

    /**
     *
     * Enter description here ...
     */
    public function getWebfileFromRequestArray()
    {

        $lastPositionOfPoint = strrpos($this->requestArray['classname'], ".");
        if ($lastPositionOfPoint != false) {
            $lastPositionOfPoint++;
        } else {
            $lastPositionOfPoint = 0;
        }

        $classnameWithoutPackagePath = substr($this->requestArray['classname'], $lastPositionOfPoint);

        /** @var MWebfile $item */
        $item = new $classnameWithoutPackagePath;
        $attributes = $item->getAttributes();

        /** @var \ReflectionProperty $attribute */
        foreach ($attributes as $attribute) {
            $attributeName = $attribute->getName();
            if (isset($this->requestArray[$attributeName])) {
                $attribute->setAccessible(true);
                $attribute->setValue($item, $this->requestArray[$attributeName]);
            }
        }
        return $item;
    }

}