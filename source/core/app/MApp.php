<?php

namespace simpleserv\webfilesframework\core\app;

use simpleserv\webfilesframework\MSite;
use simpleserv\webfilesframework\core\io\request\MUrl;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MApp extends MWebfile {
	
	public $appInformation;
	
	
	
	public static $m__sClassName = __CLASS__;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function __construct() {
	}
	
	public function getAppInformation() {
		
	}
	
	public abstract function handleUrlArguments(MUrl $url,MSite $site);

}
