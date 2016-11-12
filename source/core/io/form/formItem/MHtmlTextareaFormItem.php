<?php

namespace simpleserv\webfilesframework\core\io\form\formItem;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MHtmlTextareaFormItem extends MAbstractFormItem
{

    public function init()
    {
        $this->code = "<div style=\"margin-top:4px; width: 600px;\">";
        if (!empty($this->localizedName)) {
            $this->code .= $this->localizedName;
        } else {
            $this->code .= $this->name;
        }
        $this->code .= "<br />" .
            "<textarea
									type=\"text\"
									name=\"" . $this->name . "\"
									style=\"width: 200px;\"
									dojoType=\"dijit.Editor\"
									data-dojo-props=\"extraPlugins:['foreColor','hiliteColor','|','createLink','insertImage','fullscreen','viewsource','newpage']\">" . $this->value . "</textarea>" .
            "</div>";
    }


}
