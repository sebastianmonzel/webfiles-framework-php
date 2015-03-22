<?php

namespace \simpleserv\webfiles-framework\core\io\form;

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
 * @package    de.simpleserv.core.form
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
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
	
	public static function getDropdownMenueFormItemByTemplate($formItemName,$template,$selectedValue, $localizedName = "") {
		
		$webfiles = MSite::getInstance()->getDefaultDatastore()->getByTemplate($template);
		
		$formItem = new MDropdownMenueFormItem($formItemName,$selectedValue,$localizedName);
		
		$possibleValues = array();
		foreach ($webfiles as $webfile) {
			array_push($possibleValues, $webfile);
		}
		$formItem->setPossibleValues($possibleValues);
		
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