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
 * @copyright  2009-2013 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MWrappedSiteContent extends MSiteElement {
	
	public $m_sTitle;
	public $m_sIntroduction;
	public $m_sWrappedContentUrl;

	public static $m__sClassName = __CLASS__;

	public function __construct() {
		parent::__construct();
	}

	public function getTitle() {
		return $this->m_sTitle;
	}

	public function setTitle($p_sTitle) {
		$this->m_sTitle = $p_sTitle;
	}

	public function getIntroduction() {
		return $this->m_sIntroduction;
	}

	public function setIntroduction($introduction) {
		$this->m_sIntroduction = $introduction;
	}
	
	public function getWrappedContentUrl() {
		return $this->m_sWrappedContentUrl;
	}
	
	public function setWrappedContentUrl($wrappedContent) {
		$this->m_sWrappedContentUrl = $wrappedContent;
	}
	
}

?>