<?php

namespace \simpleserv\webfiles-framework\core\app;

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
