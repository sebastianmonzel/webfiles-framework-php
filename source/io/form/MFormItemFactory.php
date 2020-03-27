<?php

namespace webfilesframework\io\form;

use webfilesframework\io\form\formItem\MCheckboxesFormItem;
use webfilesframework\io\form\formItem\MDateFormItem;
use webfilesframework\io\form\formItem\MDropdownMenueFormItem;
use webfilesframework\io\form\formItem\MHtmlTextareaFormItem;
use webfilesframework\io\form\formItem\MTextareaFormItem;
use webfilesframework\io\form\formItem\MTextfieldFormItem;
use webfilesframework\io\form\formItem\MTimeFormItem;
use webfilesframework\io\form\formItem\MWeekdayFormItem;
use webfilesframework\MSite;


/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MFormItemFactory
{

    /**
     * Creates a MFormItem by a given attributeName.
     *
     * @param string $attributeName
     * @param $attributeValue
     * @param string $localizedName
     * @return MFormItem
     * @internal param string $value
     */
    public static function getFormItemByAttributeName($attributeName, $attributeValue, $localizedName = "")
    {

        $attributeType = substr($attributeName, 2, 1);

        if ($attributeType == "s") {
            $formItem = static::getFormItemByAttributeType("shorttext", $attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "l") {
            $formItem = static::getFormItemByAttributeType("longtext", $attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "h") {
            $formItem = static::getFormItemByAttributeType("htmllongtext", $attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "t") {
            $formItem = static::getFormItemByAttributeType("time", $attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "d") {
            $formItem = static::getFormItemByAttributeType("date", $attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "w") {
            $formItem = static::getFormItemByAttributeType("weekday", $attributeName, $attributeValue, $localizedName);
        } else {
            $formItem = static::getFormItemByAttributeType("shorttext", $attributeName, $attributeValue, $localizedName);
        }

        return $formItem;

    }

    /**
     *
     * @param string $attributeType
     * @param string $attributeName
     * @param string $attributeValue
     * @param string $localizedName
     * @return MAbstractFormItem
     */
    public static function getFormItemByAttributeType($attributeType, $attributeName, $attributeValue = "", $localizedName = "")
    {

        if ($attributeType == "integer") {
            $formItem = new MTextfieldFormItem($attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "shorttext") {
            $formItem = new MTextfieldFormItem($attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "longtext") {
            $formItem = new MTextareaFormItem($attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "htmllongtext") {
            $formItem = new MHtmlTextareaFormItem($attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "time") {
            $formItem = new MTimeFormItem($attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "date") {
            $formItem = new MDateFormItem($attributeName, $attributeValue, $localizedName);
        } else if ($attributeType == "weekday") {
            $formItem = new MWeekdayFormItem($attributeName, $attributeValue, $localizedName);
        } else {
            $formItem = new MTextfieldFormItem($attributeName, $attributeValue, $localizedName);
        }
        return $formItem;

    }


    public static function getDropdownMenueFormItemByTemplate($formItemName, $template, $selectedValue, $isFiltered = false, $localizedName = "")
    {


        $formItem = new MDropdownMenueFormItem($formItemName, $selectedValue, $localizedName, $isFiltered);

        if (!$isFiltered) {
            $webfiles = MSite::getInstance()->getDefaultDatastore()->getByTemplate($template);
            $possibleValues = array();
            foreach ($webfiles as $webfile) {
                array_push($possibleValues, $webfile);
            }
            $formItem->setPossibleValues($possibleValues);
        }

        return $formItem;

    }

    public static function getCheckboxesFormItemByTemplate($formItemName, $template, $selectedValues)
    {
        $webfiles = MSite::getInstance()->getDefaultDatastore()->getByTemplate($template);

        $formItem = new MCheckboxesFormItem($formItemName, null);

        $possibleValues = array();
        foreach ($webfiles as $webfile) {
            array_push($possibleValues, $webfile);
        }
        $formItem->setPossibleValues($possibleValues);
        $formItem->setSelectedValues($selectedValues);

        return $formItem;

    }

}