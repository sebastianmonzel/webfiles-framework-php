<?php

namespace \simpleserv\webfiles-framework\core\io\form\formItem;

/**
 * 
 * @author semo
 *
 */
class MHiddenFormItem extends MAbstractFormItem {

	public function init() {
		$this->code = "<input
							name=\"" . $this->name . "\"
							value=\"" . $this->value . "\"
							type=\"hidden\">";
	}


}
