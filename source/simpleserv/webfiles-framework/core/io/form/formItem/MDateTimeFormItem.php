<?php

namespace \simpleserv\webfiles-framework\core\io\form\formItem;

/**
 * 
 * @author semo
 *
 */
abstract class MDateTimeFormItem extends MAbstractFormItem {

	public function init() {
		$this->code = 	"<div style=\"margin-top:4px;\">" .
				"<label style=\"width:160px;display:block;float:left;\">";
		if ( ! empty($this->localizedName) ) {
			$this->code .= $this->localizedName;
		} else {
			$this->code .= $this->name;
		}
		$this->code .= "</label>
							<input
								name=\"" . $this->name . "\"
								value=\"" . $this->value . "\"
								size=\"36\">
						</div>";
			
	}
	

}