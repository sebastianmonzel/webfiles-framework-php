<?php

namespace simpleserv\webfilesframework\core\app;

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

