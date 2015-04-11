<?php

use \simpleserv\webfilesframework\core\io\request\MUrl;

/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 *
 * description
 *
 * @package    de.simpleserv
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 SimpleServ
 * @link       http://www.simpleserv.de/
*/

require_once('./const.php');
require_once(INIT_FILE);

$url = new MUrl($_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] . "?" . $_SERVER['QUERY_STRING']);

if ( $url->paramExists("batchjob") ) {
	include(CUSTOM_BATCHJOB_FOLDER . "/" . $url->getParam("batchjob") . ".batchjob.php");
} else {
	include(CUSTOM_BATCHJOB_FOLDER . "/index.batchjob.php");
}

