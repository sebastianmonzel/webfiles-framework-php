<?php

namespace webfilesframework\io\form\formItem;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractFormItem
{

    protected $name;
    protected $localizedName;
    protected $type;
    protected $code;
    protected $value;

    protected $labelWidth = 180;

    function __construct($name, $value, $localizedName = "")
    {
        $this->name = $name;
        $this->value = $value;
        $this->localizedName = $localizedName;

        $this->init();
    }

    public abstract function init();

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLabelWidth()
    {
        return $this->labelWidth;
    }

    public function setLabelWidth($labelWidth)
    {
        $this->labelWidth = $labelWidth;
    }
}
