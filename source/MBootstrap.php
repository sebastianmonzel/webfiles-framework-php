<?php

namespace simpleserv\webfilesframework; 

use simpleserv\webfilesframework\template\MTemplate;
use simpleserv\webfilesframework\core\datasystem\file\system\MFile;
use simpleserv\webfilesframework\core\authentication\MNotEnoughRightsException;
use simpleserv\webfilesframework\core\authentication\MSession;
use simpleserv\webfilesframework\core\authentication\MUser;
use simpleserv\webfilesframework\core\io\request\MUrl;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MBootstrap {
	
	protected $isAuthentificationEnabled;
	protected $loginTemplate;
	
	public function __construct() {
		$this->isAuthentificationEnabled = false;
	}
	
	public function handleSiteRequest($siteTitle, MFile $siteTemplateFile) {
		
		try {
			
			if ( $this->isAuthentificationEnabled ) {
				$this->checkForAuthorization();
			}
			
			MSite::getInstance()->setTitle($siteTitle);
			MSite::getInstance()->addCssFile(new MFile("./custom/data/css/style.css"));
			
			//-- set main site template
			$siteTemplate = new MTemplate();
			$siteTemplate->setContentByFile($siteTemplateFile);
			MSite::getInstance()->setTemplate($siteTemplate);
			
			
			if ( MUrl::getInstance()->paramExists("site") ) {
				$siteDirectory = new MDirectory("./custom/site/");
				$siteDirectoryFiles = $siteDirectory->getFileNames();
			
				$actualSiteFileName = MUrl::getInstance()->getParam("site") . ".site.php";
			
				if ( in_array($actualSiteFileName, $siteDirectoryFiles) ) {
					include("./custom/site/" . $actualSiteFileName );
				} else {
					include("./custom/site/index.site.php");
				}
			
			} else {
				include("./custom/site/index.site.php");
			}
			
		} catch (MNotEnoughRightsException $e) {
			MSite::getInstance()->setTemplate($this->loginTemplate);
			
		} catch (Exception $e) {
			MSite::getInstance()->addContent("Exception occured: <b>" . $e->getMessage() . "</b><br />");
			MSite::getInstance()->addContent("<pre>" . $e->getTraceAsString() . "</pre>");
		}
		
		echo MSite::getInstance()->getCode();
	}
	
	public function handleBatchjobRequest() {
		try {
			
			if ( $this->isAuthentificationEnabled ) {
				$this->checkForAuthorization();
			}
			
			if ( MUrl::getInstance()->paramExists("batchjob") ) {
				include("./custom/batchjob/" . MUrl::getInstance()->getParam("batchjob") . ".batchjob.php");
			} else {
				include("./custom/batchjob/index.batchjob.php");
			}
		} catch (MNotEnoughRightsException $e) {
			MSite::getInstance()->setTemplate($this->loginTemplate);
			
		} catch (Exception $e) {
			MSite::getInstance()->addContent("Exception occured: <b>" . $e->getMessage() . "</b><br />");
			MSite::getInstance()->addContent("<pre>" . $e->getTraceAsString() . "</pre>");
		}
	}
	
	public function enableAuthentification(MFile $loginTemplateFile) {

		$this->isAuthentificationEnabled = true;
		
		$loginTemplate = new MTemplate();
		$loginTemplate->setContentByFile($loginTemplateFile);
		$this->loginTemplate = $loginTemplate;
		
	}
	
	private function checkForAuthorization() {
		if ( ! MSession::getInstance()->isValid() ) {
			
			if ( isset($_POST['name']) ) {
				
				$servant = new MUser();
				$servant->presetDefaultForTemplate();
				$servant->setUsername($_POST['name']);
					
				if ( ! MSession::getInstance()->login($servant, $_POST['name'], $_POST['password']) ) {
					throw new MNotEnoughRightsException("error");
				}
					
			} else {
				throw new MNotEnoughRightsException("error");
			}
		
		}
	}
	
	
	public static function getIpAddress() {
		
		return $_SERVER['REMOTE_ADDR'];
	}
	
	
	public static function getClientAgent() {
		
		return $_SERVER['HTTP_USER_AGENT'];
	}
}