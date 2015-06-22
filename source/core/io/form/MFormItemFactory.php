<?php

namespace simpleserv\webfilesframework\core\io\form;

use simpleserv\webfilesframework\MSite;
use simpleserv\webfilesframework\core\io\form\formItem\MTextfieldFormItem;
use simpleserv\webfilesframework\core\io\form\formItem\MTextareaFormItem;
use simpleserv\webfilesframework\core\io\form\formItem\MHtmlTextareaFormItem;
use simpleserv\webfilesframework\core\io\form\formItem\MTimeFormItem;
use simpleserv\webfilesframework\core\io\form\formItem\MDateFormItem;
use simpleserv\webfilesframework\core\io\form\formItem\MWeekdayFormItem;
use simpleserv\webfilesframework\core\io\form\formItem\MDropdownMenueFormItem;
use simpleserv\webfilesframework\core\io\form\formItem\MCheckboxesFormItem;


/**
 * description
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MFormItemFactory {
	
	/**
	 * Creates a MFormItem by a given attributeName.
	 * 
	 * @param unknown_type $attributeName
	 * @param unknown_type $value
	 * @return MFormItem
	 */
	public static function getFormItemByAttributeName($attributeName, $attributeValue, $localizedName = "") {
		
		$attributeType = substr($attributeName, 2,1);
				
		if ( $attributeType == "s" ) {
			$formItem = static::getFormItemByAttributeType("shorttext", $attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "l" ) {
			$formItem = static::getFormItemByAttributeType("longtext", $attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "h" ) {
			$formItem = static::getFormItemByAttributeType("htmllongtext", $attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "t" ) {
			$formItem = static::getFormItemByAttributeType("time", $attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "d" ) {
			$formItem = static::getFormItemByAttributeType("date", $attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "w" ) {
			$formItem = static::getFormItemByAttributeType("weekday", $attributeName, $attributeValue, $localizedName);
		} else {
			$formItem = static::getFormItemByAttributeType("shorttext", $attributeName, $attributeValue, $localizedName);
		}
		
		return $formItem;
		
	}
	
	/**
	 * 
	 * @param unknown_type $attributeType
	 * @param unknown_type $attributeName
	 * @param unknown_type $attributeValue
	 * @return MAbstractFormItem
	 */
	public static function getFormItemByAttributeType($attributeType, $attributeName, $attributeValue = "", $localizedName = "") {
		
		if ( $attributeType == "integer" ) {
			$formItem = new MTextfieldFormItem($attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "shorttext" ) {
			$formItem = new MTextfieldFormItem($attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "longtext" ) {
			$formItem = new MTextareaFormItem($attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "htmllongtext" ) {
			$formItem = new MHtmlTextareaFormItem($attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "time" ) {
			$formItem = new MTimeFormItem($attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "date" ) {
			$formItem = new MDateFormItem($attributeName, $attributeValue, $localizedName);
		} else if ( $attributeType == "weekday" ) {
			$formItem = new MWeekdayFormItem($attributeName, $attributeValue, $localizedName);
		} else {
			$formItem = new MTextfieldFormItem($attributeName, $attributeValue, $localizedName);
		}
		return $formItem;
		
	}
	
	
	public static function getDropdownMenueFormItemByTemplate($formItemName,$template,$selectedValue, $isFiltered = false, $localizedName = "") {
		
		
		$formItem = new MDropdownMenueFormItem($formItemName,$selectedValue,$localizedName,$isFiltered);
		
		if ( ! $isFiltered ) {
			$webfiles = MSite::getInstance()->getDefaultDatastore()->getByTemplate($template);
			$possibleValues = array();
			foreach ($webfiles as $webfile) {
				array_push($possibleValues, $webfile);
			}
			$formItem->setPossibleValues($possibleValues);
		}
		
		return $formItem;
		
	}
	
	public static function getCheckboxesFormItemByTemplate($formItemName,$template,$selectedValues) {
		$webfiles = MSite::getInstance()->getDefaultDatastore()->getByTemplate($template);
		
		$formItem = new MCheckboxesFormItem($formItemName,null);
		
		$possibleValues = array();
		foreach ($webfiles as $webfile) {
			array_push($possibleValues, $webfile);
		}
		$formItem->setPossibleValues($possibleValues);
		$formItem->setSelectedValues($selectedValues);
		
		return $formItem;
		
	}
	
}