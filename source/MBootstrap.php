<?php

namespace simpleserv\webfilesframework;

use \simpleserv\webfilesframework\template\MTemplate;
use \simpleserv\webfilesframework\core\datasystem\file\system\MFile;
use \simpleserv\webfilesframework\core\authentication\MNotEnoughRightsException;
use \simpleserv\webfilesframework\core\authentication\MSession;
use \simpleserv\webfilesframework\core\authentication\MUser;
use \simpleserv\webfilesframework\core\io\request\MUrl;
use \simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;

class MBootstrap {
	
	protected $isAuthentificationEnabled;
	protected $loginTemplate;
	
	public function __construct() {
		$this->isAuthentificationEnabled = false;
	}
	
	public function handleSiteRequest($siteTitle,$siteTemplateFile) {
		
		try {
			
			if ( $this->isAuthentificationEnabled ) {
				$this->checkForAuthorization();
			}
			
			MSite::getInstance()->setTitle($siteTitle);
			MSite::getInstance()->addCssFile(new MFile("./custom/data/css/style.css"));
			
			//-- set main site template
			$siteTemplate = new MTemplate();
			
			//$siteTemplateFile = new MFile("./custom/template/main.tpl");
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
			$loginTemplate = new MTemplate();
			$loginTemplateFile = new MFile("./custom/login.tpl");
			$loginTemplate->setContentByFile($loginTemplateFile);
			MSite::getInstance()->setTemplate($loginTemplateFile);
			
		} catch (Exception $e) {
			MSite::getInstance()->addContent("Exception occured: <b>" . $e->getMessage() . "</b><br />");
			MSite::getInstance()->addContent("<pre>" . $e->getTraceAsString() . "</pre>");
		}
		
		echo MSite::getInstance()->getCode();
	}
	
	public function handleBatchjobRequest() {
		
		if ( MUrl::getInstance()->paramExists("batchjob") ) {
			include("./custom/batchjob/" . MUrl::getInstance()->getParam("batchjob") . ".batchjob.php");
		} else {
			include("./custom/batchjob/index.batchjob.php");
		}
	}
	
	public function enableAuthentification(MTemplate $loginTemplate) {
		$this->isAuthentificationEnabled = true;
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
}