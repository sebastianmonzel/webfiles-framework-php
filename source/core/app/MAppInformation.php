<?php

namespace simpleserv\webfilesframework\core\app;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MAppInformation extends MWebfile {
	
	public $m_sAppName;
	public $m_sPackageName;
	
	public $m_sAuthorName = "simpleserv company";
	public $m_sAuthorWebsite = "http://www.simpleserv.de/";
	
	
	public static $m__sClassName = __CLASS__;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function __construct() {
		
	}
	
}

