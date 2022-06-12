<?php

namespace webfilesframework\io\form\webfile;

use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\io\form\formItem\MHiddenFormItem;
use webfilesframework\io\form\MForm;
use webfilesframework\io\form\MFormItemFactory;
use webfilesframework\io\request\MUrl;

/**
 * Generates a form according to a given webfile.<br/>
 * Uses the javascript-libary DojoToolkit for visualizing
 * form elements.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWebfileFormVisualizer
{

    private $webfile;

    private $ignoredFieldsList;
    private $localizedNamesList;
    private $hiddenFieldsList;

    /** @var MForm $form */
    private $form;

    public function __construct(MWebfile $webfile)
    {
        $this->init($webfile);
    }

    public function init(MWebfile $webfile)
    {

        $this->webfile = $webfile;
        if (!isset($this->ignoredFieldsList)) {
            $this->ignoredFieldsList = array();
        }

        if (!isset($this->hiddenFieldsList)) {
            $this->hiddenFieldsList = array();
        }

        $action = "index.php?" . MUrl::getInstance()->getQueryString();
        $method = "POST";
        $this->form = new MForm($action, $method);

        $attributes = $this->webfile->getAttributes();

        /** @var \ReflectionProperty $attribute */
        foreach ($attributes as $attribute) {
            $attributeName = $attribute->getName();

            $attribute->setAccessible(true);
            if (
                MWebfile::isSimpleDatatypeAttribute($attributeName)
                && !array_key_exists($attributeName, $this->ignoredFieldsList)
                && !in_array($attributeName, $this->ignoredFieldsList)
            ) {

                $attributeValue = $attribute->getValue($this->webfile);

                if (!array_key_exists($attributeName, $this->hiddenFieldsList)
                    && !in_array($attributeName, $this->hiddenFieldsList)
                ) {


                    // NOT HIDDEN FIELD
                    if (isset($this->localizedNamesList[$attributeName])) {
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

    public function setWebfile(MWebfile $webfile)
    {
        $this->webfile = $webfile;
    }

    /**
     *
     * @return string
     */
    public function getCode()
    {
        return $this->form->getCode();
    }

    public function setIgnoredFieldsList($ignoredFieldsList)
    {
        $this->ignoredFieldsList = $ignoredFieldsList;
    }

    public function setHiddenFieldsList($hiddenFieldsList)
    {
        $this->hiddenFieldsList = $hiddenFieldsList;
    }

    public function setLocalizedNamesList($fieldNameList)
    {
        $this->localizedNamesList = $fieldNameList;
    }

    /**
     * @return MForm
     */
    public function getForm()
    {
        return $this->form;
    }

    public function setLabelWidthOnEachFormItem($labelWidth)
    {
        $this->form->setLabelWidthOnEachFormItem($labelWidth);
    }

}
