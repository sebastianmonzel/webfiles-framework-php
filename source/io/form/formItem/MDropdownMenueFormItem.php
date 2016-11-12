<?php

namespace simpleserv\webfilesframework\core\io\form\formItem;

use simpleserv\webfilesframework\MSite;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDropdownMenueFormItem extends MAbstractFormItem
{

    protected $possibleValues;
    protected $filtered;

    protected $initialized = false;

    public function __construct($name, $value, $localizedName = "", $filtered = false)
    {

        $this->filtered = $filtered;
        parent::__construct($name, $value, $localizedName);
    }

    public function setPossibleValues($possibleValues)
    {
        $this->possibleValues = $possibleValues;
    }

    public function getPossibleValues()
    {
        return $this->possibleValues;
    }

    public function init($useLabel = true)
    {

        $this->code = "";

        if ($useLabel) {
            $this->code .= "<div style=\"margin-top:4px;\">";

            if (!empty($this->localizedName)) {
                $this->code .= $this->localizedName;
            } else {
                $this->code .= $this->name;
            }

            $this->code = "<div style=\"margin-top:4px;\">
								<label style=\"width:" . $this->getLabelWidth() . "px;display:block;float:left;\">";
            if (!empty($this->localizedName)) {
                $this->code .= $this->localizedName;
            } else {
                $this->code .= $this->name;
            }
            $this->code .= "	</label>
								";
        }

        if (!$this->filtered) {
            $this->code .= "<select name=\"" . $this->name . "\" dojoType=\"dijit.form.Select\"";
            $this->code .= ">";

            if (is_array($this->possibleValues)) {
                foreach ($this->possibleValues as $value) {
                    $this->code .= "<option value=\"" . $value->getId() . "\"";
                    if ($value->getId() == $this->value) {
                        $this->code .= " selected=\"selected\"";
                    }
                    $this->code .= ">" . $value . "</option>";
                }
            }
            $this->code .= "		</select>";
        } else {


            if (!$this->initialized) {
                MSite::getInstance()->addHeader('<script type="text/javascript">
		
	require([
	         "dijit/form/ComboBox", "dijit/form/FilteringSelect", "dojo/on","dojo/dom"
	     ], function(ComboBox, FilteringSelect, on, dom){
	
	         new dijit.form.FilteringSelect({
	             id: "' . $this->name . '",
				 name: "' . $this->name . '",
	             autoComplete: true,
	             style: "width: 300px;",
	             searchDelay: 1000
	         }, "' . $this->name . '").startup();
	         on(dom.byId("' . $this->name . '"), "keyup", function(event) {
	        	
	          	require([
	                             "dojo/store/Memory","dojo/request/xhr", "dojo/dom", "dojo/dom-construct", "dojo/json","dojo/domReady!"
	                         ], function(Memory, xhr, dom, domConst, JSON){
							      	    
	     	    xhr("index.php?site=makeAjaxRequest&searchstring=" + encodeURIComponent(dijit.byId("' . $this->name . '").get(\'displayedValue\')), {
	     	    	preventCache: "true"
	     	    }).then(function(data){
	     	    	var datastore = new Memory(JSON.parse(data));
	     	    	dijit.byId("' . $this->name . '").set("store",datastore);
	     	      }, function(err){
	     	        domConst.place("<p>error: <p>" + err.response.text + "</p></p>", "output");
	     	      });
	     	  })
	         });
	     });
		</script>');
            }

            $this->initialized = true;

            $this->code .= "<input id=\"" . $this->name . "\" />";
        }

        if ($useLabel) {
            $this->code .= "
							<div style=\"clear:both;\"></div>
						</div>";
        }

    }

    public function getCode($useLabel = true)
    {
        $this->init($useLabel);
        return parent::getCode();
    }

}