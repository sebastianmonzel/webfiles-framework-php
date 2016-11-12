<?php

namespace simpleserv\webfilesframework\core\io\form;

use simpleserv\webfilesframework\MSite;
use simpleserv\webfilesframework\core\io\form\formItem\MAbstractFormItem;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MForm
{

    private $action;
    private $method;

    private $formItems;

    private $useSubmitButton = true;
    private $submitButtonText = "Submit";

    /**
     *
     * Enter description here ...
     * @param String $action
     * @param String $method
     */
    public function __construct($action, $method)
    {
        $this->action = $action;
        $this->method = $method;

        $this->formItems = array();

    }


    /**
     * Enter description here ...
     */
    function getCode()
    {

        $out = $this->getHeaderCode();
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
        $out = "<form method=\"" . $this->method . "\" action=\"" . $this->action . "\" >";
        return $out;
    }

    /**
     *
     * Enter description here ...
     */
    function getFooterCode()
    {
        $out = "";
        if ($this->useSubmitButton) {
            $out = "<div>";
            $out .= "<input type=\"submit\" value=\"absenden\" dojoType=\"dijit.form.Button\" id=\"submitButton\" label=\"" . $this->submitButtonText . "\">";
            $out .= "</div>";

        }
        $out .= "</form>";

        return $out;
    }

    function getFormItemsCode()
    {

        $out = "";

        foreach ($this->formItems as $value) {
            $out .= $value->getCode();
        }

        return $out;
    }

    /**
     *
     * @param MAbstractFormItem $formItem
     * @param string $precedingFormItemName
     */
    function addFormItem(MAbstractFormItem $formItem, $precedingFormItemName = null)
    {

        if ($precedingFormItemName == null) {
            array_push($this->formItems, $formItem);
        } else {

            $formItems = array();

            foreach ($this->formItems as $iteratingFormItem) {

                $formItems[] = $iteratingFormItem;
                if ($iteratingFormItem->getName() == $precedingFormItemName) {
                    $formItems[] = $formItem;
                }
            }

            $this->formItems = $formItems;

        }

    }

    /**
     *
     * Enter description here ...
     * @param String $action
     */
    function setAction($action)
    {
        $this->action = $action;
    }

    /**
     *
     * Enter description here ...
     * @param $method
     */
    function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     *
     * Enter description here ...
     * @param $name
     */
    function setName($name)
    {
        $this->name = $name;
    }

    public static function addFormHeadersToSite($site)
    {


        $site->addHeader("<link rel=\"stylesheet\" href=\"http://ajax.googleapis.com/ajax/libs/dojo/1.8.10/dijit/themes/claro/claro.css\">");
        $site->addHeader("<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/dojo/1.8.10/dojo/dojo.js\" data-dojo-config=\"isDebug: true, parseOnLoad: true\"></script>");
        $site->addHeader("<script type=\"text/javascript\">
		
		
		dojo.require(\"dojo.on\");
		dojo.require(\"dojox.validate\");
		dojo.require(\"dojox.validate.us\");
		dojo.require(\"dojox.validate.web\");
		
		
		/* basic dijit classes */
		dojo.require(\"dijit.dijit\");
		dojo.require(\"dijit.form.Form\");
		dojo.require(\"dijit.form.Button\");
		dojo.require(\"dijit.form.Select\");
		dojo.require(\"dijit.form.FilteringSelect\");
		dojo.require(\"dijit.form.TextBox\");
		dojo.require(\"dijit.form.ValidationTextBox\");
		dojo.require(\"dijit.form.DateTextBox\");
		dojo.require(\"dijit.form.TimeTextBox\");
		dojo.require(\"dijit.form.Textarea\");
		dojo.require(\"dijit.form.CheckBox\");
		
		dojo.require(\"dijit.Dialog\");
		dojo.require(\"dijit.Editor\");
			
		
		dojo.require(\"dojo.dom\");
		dojo.require(\"dojo.parser\");
		dojo.require(\"dojox.validate\");
		</script>
		");

        MSite::getInstance()->addBodyAttribute('id', 'ff-meridian');
        MSite::getInstance()->addBodyAttribute('class', 'claro');

    }

    public function setUseSubmitButton($useSubmitButton)
    {
        $this->useSubmitButton = $useSubmitButton;
    }

    public function setSubmitButtonText($submitButtonText)
    {
        $this->submitButtonText = $submitButtonText;
    }

    public function setLabelWidthOnEachFormItem($labelWidth)
    {

        foreach ($this->formItems as $formItem) {
            $formItem->setLabelWidth($labelWidth);
            $formItem->init();
        }
    }
}