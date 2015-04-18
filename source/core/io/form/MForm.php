<?php

namespace simpleserv\webfilesframework\core\io\form;

use simpleserv\webfilesframework\core\io\form\formItem\MAbstractFormItem;

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
class MForm {
	
	private $action;
	private $method;
	
	private $formItems;
	
	private $useSubmitButton = true;
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $action
	 * @param unknown_type $method
	 */
	public function __construct($action,$method)
	{
		$this->action = $action;
		$this->method = $method;
		
		$this->formItems = array();
		
	}
	
	
	/**
	 * Enter description here ...
	 */
	function getCode() {				
		
		$out  = $this->getHeaderCode();
		$out .= $this->getFormItemsCode();
		$out .= $this->getFooterCode();
		
		return $out;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	function getHeaderCode()
	{
		$out  = "<form method=\"" . $this->method . "\" action=\"" . $this->action . "\" >";
		return $out;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	function getFooterCode()
	{
		$out = "";
		if ( $this->useSubmitButton ) {
			$out = "<div>";
			$out .= "<input type=\"submit\" value=\"absenden\" dojoType=\"dijit.form.Button\" id=\"submitButton\" label=\"Submit\">";
			$out .= "</div>";
			
		}
		$out .= "</form>";
		
		return $out;
	}
	
	function getFormItemsCode() {
		
		$out = "";
		
		foreach ($this->formItems as $value) {
			$out .= $value->getCode();
		}
		
		return $out;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $formItem
	 */
	function addFormItem(MAbstractFormItem $formItem) {
		array_push($this->formItems, $formItem);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $action
	 */
	function setAction($action) {
		$this->action = $action;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $method
	 */
	function setMethod($method) {
		$this->method = $method;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $name
	 */
	function setName($name) {
		$this->name = $name;
	}
	
	public static function addFormHeadersToSite($site) {
		$site->addHeader("<link rel=\"stylesheet\" href=\"http://ajax.googleapis.com/ajax/libs/dojo/1.7.2/dijit/themes/claro/claro.css\">");
		$site->addHeader("<link rel=\"stylesheet\" href=\"./style.css\">");
		$site->addHeader("<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/dojo/1.7.2/dojo/dojo.js\" data-dojo-config=\"isDebug: true, parseOnLoad: true\"></script>");
		$site->addHeader("<script type=\"text/javascript\">
			dojo.require('dojox.validate');
			dojo.require('dojox.validate.us');
			dojo.require('dojox.validate.web');
			/* basic dijit classes */

			dojo.require('dijit.dijit');
			dojo.require('dijit.form.Form');
			dojo.require('dijit.form.Button');
			dojo.require('dijit.form.TextBox');
			dojo.require('dijit.form.ValidationTextBox');
			dojo.require('dijit.form.DateTextBox');
			dojo.require('dijit.form.TimeTextBox');
			dojo.require('dijit.form.Textarea');
			dojo.require('dijit.Editor');
			dojo.require('dojo.dom');
			dojo.require('dojo.parser');
			dojo.require('dojox.validate');
			
			dojo.require('dijit._editor.plugins.TextColor');
			dojo.require('dijit._editor.plugins.LinkDialog');
			dojo.require('dijit._editor.plugins.FullScreen');
			dojo.require('dijit._editor.plugins.ViewSource');
			dojo.require('dijit._editor.plugins.NewPage');
			
</script>
		");

	}
	
	public function setUseSubmitButton($useSubmitButton) {
		$this->useSubmitButton = $useSubmitButton;
	}
	
}