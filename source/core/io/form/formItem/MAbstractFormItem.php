<?php

namespace simpleserv\webfilesframework\core\io\form\formItem;

/**
 * 
 * @author semo
 *
 */
abstract class MAbstractFormItem extends MWebfile {
	
	protected $name;
	protected $localizedName;
	protected $type;
	protected $code;
	protected $value;
	
	
	function __construct($name,$value,$localizedName = "") {
		$this->name          = $name;
		$this->value         = $value;
		$this->localizedName = $localizedName;
		
		$this->init();
	}
	
	public abstract function init();

	public function getCode() {
		return $this->code;
	}
}
