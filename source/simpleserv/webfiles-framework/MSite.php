<?php

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
 * @package    de.simpleserv
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MSite {
	
	
	private $title;
	
	private $content;
	private $header;
	
	private $template;
	
	private $bodyAttributeList;
	
	private $defaultDatastore;
	
	private $isBlank = false;
	
	private $isAuthorisationNeeded = false;
	
	private static $instance = null;
	
	public function __construct() {
		$this->bodyAttributeList = array();
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return MSite
	 */
	public static function getInstance() {
		if ( MSite::$instance == null ) {
			MSite::$instance = new MSite();
		}
		return MSite::$instance;
	}
	
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function addCssFile(MFile $file) {
		//if ( $file->exists() ) {
			$this->addHeader("<link rel=\"stylesheet\" href=\"" . $file->getName() . "\" type=\"text/css\" media=\"screen\" />");
		//}
	}
	
	public function setTemplate(MTemplate $template) {
		$this->template = $template; 
	}
	
	public function addHeader($header) {
		$this->header = $this->header . $header;
	}
	
	public function addBodyAttribute($attributeName, $attributeContent) {
		$this->bodyAttributeList[$attributeName] = $attributeContent;
	}
	
	public function setBlank($isBlank) {
		$this->isBlank = $isBlank;
	}
	
	public function addContent($content) {
		$this->content = $this->content . $content;
	}
	
	public function isAuthorisationNeeded() {
		return $this->isAuthorisationNeeded;
	}
	
	public function setAuthorisationNeeded($isAuthorisationNeeded) {
		$this->isAuthorisationNeeded = $isAuthorisationNeeded;
	}
	
	public function setDefaultDatastore(MAbstractDatastore $defaultDatastore) {
		$this->defaultDatastore = $defaultDatastore;
	}
	
	/**
	 * 
	 * @return MAbstractDatastore
	 */
	public function getDefaultDatastore() {
		if ( $this->defaultDatastore == null ) {
			$this->initDefaultDatastore();
		}
		return $this->defaultDatastore;
	}
	
	private function initDefaultDatastore() {
		$this->defaultDatastore = MDatastoreFactory::createDatastore(new MDatabaseConnection());
	}
	
	/**
	 * Returns the code of the site.
	 * @return MString: code.
	 */
	public function getCode() {
		if ( ! $this->isBlank ) {
			
			$out = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
	<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"de-de\" lang=\"de-de\" >";
			//$out .= "<html>";
			$out .= "<head>";
			$out .= "<title>" . $this->title . "</title>";
			$out .= $this->header;
			$out .= "</head>";
			$out .= "<body ";
			
			foreach ($this->bodyAttributeList as $key => $value) {
				$out .= $key . "=" . "\"" . $value . "\" ";
			}
			
			$out .= ">";
			
			$dataset = array();
			$dataset['content'] = $this->content;
			if ( isset($this->template) ) {
				$this->template->setDataset($dataset);
				$this->template->compileTemplate();
				$out .= $this->template->getResult();
			} else {
				$out .= $this->content;
			}
			
			$out .= "</body>";
			$out .= "</html>";
			
			return $out;
		} else {
			return "";
		}
	}
}

?>