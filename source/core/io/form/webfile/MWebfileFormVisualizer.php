<?php

namespace simpleserv\webfilesframework\core\io\form\webfile;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
use simpleserv\webfilesframework\core\io\form\MForm;
use simpleserv\webfilesframework\core\io\request\MUrl;
use simpleserv\webfilesframework\core\io\form\MFormItemFactory;
use simpleserv\webfilesframework\core\io\form\formItem\MHiddenFormItem;

/**
 * Generates a form according to a given webfile.<br/>
 * Uses the javascript-libary DojoToolkit for visualizing 
 * form elements.
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWebfileFormVisualizer {
	
	private $webfile;
	
	private $ignoredFieldsList;
	private $localizedNamesList;
	private $hiddenFieldsList;
	
	private $form;
	
	public function __construct(MWebfile $webfile) {
		$this->init($webfile);
	}
	
	public function init(MWebfile $webfile) {
		
		$this->webfile = $webfile;
		if ( ! isset($this->ignoredFieldsList) ) {
			$this->ignoredFieldsList = array();
		}
		
		if ( ! isset($this->hiddenFieldsList) ) {
			$this->hiddenFieldsList = array();
		}
		
		$this->form = new MForm("index.php?" . MUrl::getInstance()->getQueryString(), "POST");
		
		$attributes = $this->webfile->getAttributes();
		
		foreach ($attributes as $attribute) {
			$attributeName = $attribute->getName();
			
			$attribute->setAccessible(true);
			if ( 
				MWebfile::isSimpleDatatype($attributeName) 
					&& ! array_key_exists($attributeName, $this->ignoredFieldsList) 
					&& ! in_array($attributeName, $this->ignoredFieldsList) ) {
		
				$attributeValue = $attribute->getValue($this->webfile);
				
				if ( ! array_key_exists($attributeName, $this->hiddenFieldsList) 
					&& ! in_array($attributeName, $this->hiddenFieldsList) ) {
					
					
					// NOT HIDDEN FIELD
					if ( isset($this->localizedNamesList[$attributeName]) ) {
						$formItem = MFormItemFactory::getFormItemByAttributeName(
								$attributeName,
								$attributeValue,
								$this->localizedNamesList[$attributeName]
						);
					} else {
						$formItem = MFormItemFactory::getFormItemByAttributeName(
								$attributeName,
								$attributeValue
						);
					}
				} else {
					
					// HIDDEN FIELD
					$formItem = new MHiddenFormItem($attributeName, $attributeValue);
				}
		
				$this->form->addFormItem($formItem);
			}
		}
		
		// ADD ITEM FOR CLASSNAME
		$formItem = new MHiddenFormItem("classname", get_class($this->webfile));
		$this->form->addFormItem($formItem);
		
	}
	
	public function setWebfile(MWebfile $webfile) {
		$this->webfile = $webfile;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getCode() {
		return $this->form->getCode();
	}
	
	public function setIgnoredFieldsList($ignoredFieldsList) {
		$this->ignoredFieldsList = $ignoredFieldsList;
	}
	
	public function setHiddenFieldsList($hiddenFieldsList) {
		$this->hiddenFieldsList = $hiddenFieldsList;
	}
	
	public function setLocalizedNamesList($fieldNameList) {
		$this->localizedNamesList = $fieldNameList;
	}
	
	/**
	 * @return MForm
	 */
	public function getForm() {
		return $this->form;
	}
	
	public function setLabelWidthOnEachFormItem($labelWidth) {
		$this->form->setLabelWidthOnEachFormItem($labelWidth);
	}
	
}
