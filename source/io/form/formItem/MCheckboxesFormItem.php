<?php

namespace simpleserv\webfilesframework\io\form\formItem;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MCheckboxesFormItem extends MAbstractFormItem
{

    private $possibleValues;
    private $selectedValues;


    public function setPossibleValues($possibleValues)
    {
        $this->possibleValues = $possibleValues;
    }

    public function setSelectedValues($selectedValues)
    {
        $this->selectedValues = $selectedValues;
    }

    public function getPossibleValues()
    {
        return $this->possibleValues;
    }

    public function init()
    {
        $this->code = "<div style=\"margin-top:4px;\">";

        if (!empty($this->localizedName)) {
            $this->code .= $this->localizedName;
        } else {
            $this->code .= $this->name;
        }

        $this->code = "<div style=\"margin-top:4px; width:600px;\">
							<label style=\"width:" . $this->getLabelWidth() . "px;display:block;float:left;\">";
        if (!empty($this->localizedName)) {
            $this->code .= $this->localizedName;
        } else {
            $this->code .= $this->name;
        }
        $this->code .= "	</label>
							<div style=\"float:right; width:440px;\">";


        if (is_array($this->possibleValues)) {
            foreach ($this->possibleValues as $value) {
                $this->code .= "<input type=\"checkbox\" id=\"" . $this->name . "_" . $value->getId() . "\" name=\"" . $this->name . "[]\" value=\"" . $value->getId() . "\" data-dojo-type=\"dijit.form.CheckBox\"";
                if (in_array($value->getId(), $this->selectedValues)) {
                    $this->code .= " checked=\"checked\"";
                }
                $this->code .= "/><label for=\"" . $this->name . "_" . $value->getId() . "\">" . $value . "</label><br>";
            }
        }
        $this->code .= "	</div>
							<div style=\"clear:both;\"></div>
						</div>";

    }

    public function getCode()
    {
        $this->init();
        return parent::getCode();
    }

}