<?php

namespace simpleserv\webfilesframework\core\io\form\webfile;

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
     *
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

        $item = new $classnameWithoutPackagePath;
        $attributes = $item->getAttributes();

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