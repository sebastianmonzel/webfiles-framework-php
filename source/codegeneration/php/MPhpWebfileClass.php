<?php

namespace simpleserv\webfilesframework\core\codegeneration\php;

use simpleserv\webfilesframework\core\codegeneration\MAbstractClass;
use simpleserv\webfilesframework\MWebfilesFrameworkException;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MPhpWebfileClass extends MAbstractClass
{

    public function generateCode()
    {

        $this->generateSetterAndGetter();

        $code = parent::generateCode();
        return $code;
    }

    protected function generatePreambleCode()
    {
        return "<?php \n";
    }

    protected function generateHeaderCode()
    {


        return "class " . $this->className . " extends MWebfile { \n\n";
    }

    protected function generateAttributes()
    {

        $code = "";

        /** @var MPhpClassAttribute $attribute */
        foreach ($this->attributes as $attribute) {

            if (!$attribute instanceof MPhpClassAttribute) {
                throw new MWebfilesFrameworkException("Cannot generate code for attribute which is not of type 'MPhpClassAttribute'.");
            }

            // ADD "m_" BEFORE THE ATTRIBUTENAME TO MAKE IT POSSIBLE FOR WEBFILES FRAMEWORK TO RECOGNIZE RELEVANT ATTRIBUTES
            $modifiedAttribute = new MPhpClassAttribute($attribute->getVisibility(), "m_" . $attribute->getName(), $attribute->getType());

            $code .= $modifiedAttribute->generateCode() . "\n";
        }

        return $code;
    }

    protected function generateFooterCode()
    {
        return "}";
    }

    public function addAttribute(MPhpClassAttribute $attribute)
    {
        $this->attributes[] = $attribute;
    }

    public function addMethod(MPhpClassMethod $method)
    {
        $this->methods[] = $method;
    }

    private function generateSetterAndGetter()
    {
        foreach ($this->attributes as $attribute) {

            $firstLetterUppercaseAttribute = ucfirst($attribute);

            // GETTER
            $getterMethodName = "get" . $firstLetterUppercaseAttribute;
            $getterContent = "return \$this->m_" . $attribute . ";";
            $getterMethod = new MPhpClassMethod("public", $getterMethodName, $getterContent);
            $this->addMethod($getterMethod);

            // SETTER
            $setterMethodName = "set" . $firstLetterUppercaseAttribute;
            $setterContent = "\$this->m_" . $attribute . " = $" . $attribute . ";";
            $setterMethod = new MPhpClassMethod("public", $setterMethodName, $setterContent);
            $setterMethod->addParameter(new MPhpClassMethodParameter($attribute, ""));
            $this->addMethod($setterMethod);

        }
    }
}