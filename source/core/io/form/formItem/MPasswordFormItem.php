<?php

namespace simpleserv\webfilesframework\core\io\form\formItem;

/**
 * 
 * @author semo
 *
 */
class MPasswordFormItem extends MAbstractFormItem {

	public function init() {
		$this->code = 	"<div style=\"margin-top:4px;\">
							<label style=\"width:" . $this->getLabelWidth() . "px;display:block;float:left;\">";
		if ( ! empty($this->localizedName) ) {
			$this->code .= $this->localizedName;
		} else {
			$this->code .= $this->name;
		}
		$this->code .= "</label>" .
				"<input type=\"password\"
									name=\"" . $this->name . "\"
									value=\"" . $this->value . "\"
									size=\"36\">
						</div>";
	}
	

}
