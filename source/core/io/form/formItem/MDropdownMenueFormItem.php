<?php

namespace \simpleserv\webfiles-framework\core\io\form\formItem;

/**
 * 
 * @author semo
 *
 */
class MDropdownMenueFormItem extends MAbstractFormItem {
	
	protected $possibleValues;
	
	public function setPossibleValues($possibleValues) {
		$this->possibleValues = $possibleValues;
	}
	
	public function getPossibleValues() {
		return $this->possibleValues;
	}
	
	public function init($useLabel = true) {
		
		$this->code = "";
		
		if ( $useLabel ) {
			$this->code .= "<div style=\"margin-top:4px;\">";
		
			if ( ! empty($this->localizedName) ) {
				$this->code .= $this->localizedName;
			} else {
				$this->code .= $this->name;
			}
		
			$this->code = 	"<div style=\"margin-top:4px; width:600px;\">
								<label style=\"width:160px;display:block;float:left;\">";
			if ( ! empty($this->localizedName) ) {
				$this->code .= $this->localizedName;
			} else {
				$this->code .= $this->name;
			}
			$this->code .= "	</label>
								<div style=\"float:right; width:440px;margin: 0px;\">";
		}
		
		$this->code .="<div name=\"" . $this->name . "\" dojoType=\"dijit.form.Select\" style=\"margin: 0px;\">";
		
		if ( is_array($this->possibleValues)) {
			foreach ($this->possibleValues as $value) {
				$this->code .= "<span value=\"" . $value->getId() . "\"";
				if ( $value->getId() == $this->value ) {
					$this->code .= " selected=\"selected\"";
				}
				$this->code .= "><span style=\"color:#000000;\">" . $value . "</span></span>";
			}
		}
		$this->code .= "		</div>";
		if ( $useLabel ) {
			$this->code .= "</div>
						<div style=\"clear:both;\"></div>
					</div>";
		}
		
	}
	
	public function getCode($useLabel = true) {
		$this->init($useLabel);
		return parent::getCode();
	}

}