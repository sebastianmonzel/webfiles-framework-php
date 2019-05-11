<?php

namespace webfilesframework\io\form\formItem;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MHiddenFormItem extends MAbstractFormItem
{

    public function init()
    {
        $this->code = "<input
							name=\"" . $this->name . "\"
							value=\"" . $this->value . "\"
							type=\"hidden\">";
    }


}
