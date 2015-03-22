<?php

namespace \simpleserv\webfiles-framework\core\io\form\formItem;

/**
 * 
 * @author semo
 *
 */
class MTextareaFormItem extends MAbstractFormItem {
	
	public function init() {
		$this->code = 	"<div style=\"margin-top:4px;\">";
		if ( ! empty($this->localizedName) ) {
			$this->code .= $this->localizedName;
		} else {
			$this->code .= $this->name;
		}
		$this->code .= "<br />" .
				"<textarea
					name=\"" . $this->name . "\"
					style=\"width: 600px;\"
					dojoType=\"dijit.form.Textarea\">" . $this->value . "</textarea>" .
					"</div>";
	}
	
}