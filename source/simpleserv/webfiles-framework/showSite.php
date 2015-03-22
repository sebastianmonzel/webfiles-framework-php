<?php

/* #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv company
 * #########################################################
 *
 * description
 *
 * @package    de.simpleserv
 * @author     simpleserv company <info@simpleserv.com>
 * @author     Sebastian Monzel <s_monzel@simpleserv.com>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.com/
*/

$basePath = "vendor/simpleserv/webfiles-framework/";

require_once($basePath . 'const.php');

// DEFAULT CONFIG
require_once(ESSENTIAL_CONFIGURATION_FOLDER . FOLDER_SEPERATOR . 'configureCharset.php');
require_once(ESSENTIAL_CONFIGURATION_FOLDER . FOLDER_SEPERATOR . 'configureErrorHandling.php');
require_once(ESSENTIAL_CONFIGURATION_FOLDER . FOLDER_SEPERATOR . 'configureTimezone.php');

include(CUSTOM_FOLDER . "/config.php");

try {
	
	
	// TODO warum geht der hier ins timout?
	//MSession::getInstance()->init();
	
	/*if ( isset($_GET['logout']) && $_GET['logout'] == "true" ) {
		MSession::getInstance()->destroy();
	}*/
	
	MSite::getInstance()->setTitle(SITE_TITLE);
	MSite::getInstance()->addCssFile(new MFile("./custom/data/css/style.css"));
	
	//-- set main site template
	$siteTemplate = new MTemplate();
	
	$siteTemplateFile = new MFile(CUSTOM_TEMPLATE_FOLDER . "/main.tpl");
	$siteTemplate->setContentByFile($siteTemplateFile);
	MSite::getInstance()->setTemplate($siteTemplate);
	
	
	
	//@todo
	//überprüfen, ob navigation gebraucht wird (siehe configuration), wenn ja konfiguration in template ersetzen,
	//wenn nein, dann nichts tun.
	
	
	
	// CHECK FOR AUTHORISATION
	if ( MSite::getInstance()->isAuthorisationNeeded() && ! MSession::getInstance()->isValid() ) {
		
		if ( isset($_POST['name']) ) {
			
			$servant = new MServant();
			$servant->presetDefaultForTemplate();
			$servant->setUsername($_POST['name']);
			
			if ( ! MSession::getInstance()->login($servant, $_POST['name'], $_POST['password']) ) {
				throw new MNotEnoughRightsException("error");
			}
			
		} else {
			throw new MNotEnoughRightsException("error");
		}
		
	}
	
	// SHOW SITE 
	if ( MUrl::getInstance()->paramExists("site") ) {
		$siteDirectory = new MDirectory(CUSTOM_SITE_FOLDER);
		$siteDirectoryFiles = $siteDirectory->getFileNames();
		
		$actualSiteFileName = MUrl::getInstance()->getParam("site") . ".site.php";
		
		if ( in_array($actualSiteFileName, $siteDirectoryFiles) ) {
			include(CUSTOM_SITE_FOLDER . "/" . $actualSiteFileName );		
		} else {		
			include(CUSTOM_SITE_FOLDER . "/" . "index.site.php");
		}
		
	} else {
		include(CUSTOM_SITE_FOLDER . "/" . "index.site.php");
	}	
	
	//-- site output	
} catch (MNotEnoughRightsException $e) {
	
	// SHOW LOGIN DIALOG
	$siteTemplate = new MTemplate();
	$siteTemplateFile = new MFile(CUSTOM_TEMPLATE_FOLDER . "/login.tpl");
	$siteTemplate->setContentByFile($siteTemplateFile);
	MSite::getInstance()->setTemplate($siteTemplate);
} catch (Exception $e) {
	MSite::getInstance()->addContent("Exception occured: <b>" . $e->getMessage() . "</b><br />");
	MSite::getInstance()->addContent("<pre>" . $e->getTraceAsString() . "</pre>");
}

echo MSite::getInstance()->getCode();
