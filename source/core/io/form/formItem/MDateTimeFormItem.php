<?php

namespace simpleserv\webfilesframework\core\io\form\formItem;

/**
 * description
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MDateTimeFormItem extends MAbstractFormItem {

	public function init() {
		$this->code = 	"<div style=\"margin-top:4px;\">" .
				"<label style=\"width:" . $this->getLabelWidth() . "px;display:block;float:left;\">";
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
