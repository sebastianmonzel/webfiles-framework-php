<?php 

namespace simpleserv\webfilesframework;

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
class MSiteContent extends MSiteElement {
	
	public $m_sTitle;
	public $m_lContent;

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

	public function getContent() {
		return $this->m_lContent;
	}

	public function setContent($content) {
		$this->m_lContent = $content;
	}

	public function addContent($content) {
		$this->m_lContent .= $content;
	}	
	
}