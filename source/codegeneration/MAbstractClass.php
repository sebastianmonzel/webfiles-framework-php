<?php

namespace simpleserv\webfilesframework\core\codegeneration;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractClass extends MAbstractCodeItem
{

    protected $className;

    protected $isAbstract = false;
    protected $visibility = "public";

    protected $attributes = array();
    protected $methods = array();


    /**
     *
     * Enter description here ...
     */
    public function __construct($className, $isAbstract, $visibility = "public")
    {

        $this->className = $className;

        $this->isAbstract = $isAbstract;
        $this->visibility = $visibility;

    }


    public function generateCode()
    {
        $code = $this->generatePreambleCode();
        $code .= $this->generateHeaderCode();

        // ---<
        $code .= $this->generateAttributesCode();
        $code .= $this->generateMethodsCode();
        // ---<

        $code .= $this->generateFooterCode();

        return $code;
    }


    protected abstract function generatePreambleCode();

    protected abstract function generateHeaderCode();

    protected function generateAttributesCode()
    {
        $code = "";
        foreach ($this->attributes as $attribute) {
            $code .= $attribute->generateCode();
        }
        return $code;
    }

    protected function generateMethodsCode()
    {
        $code = "";
        foreach ($this->methods as $method) {
            $code .= $method->generateCode();
        }
        return $code;
    }

    protected abstract function generateFooterCode();
}
