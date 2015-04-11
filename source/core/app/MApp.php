<?php

namespace simpleserv\webfilesframework\core\app;

use \simpleserv\webfilesframework\MSite;
use \simpleserv\webfilesframework\core\io\request\MUrl;
use \simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 */

/**
 * description
 *
 * @package    de.simpleserv.core.app
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
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
