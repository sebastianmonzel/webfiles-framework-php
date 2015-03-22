<?php

namespace \simpleserv\webfiles-framework\core\datastore\types\mail;

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
 * @package    de.simpleserv.core.datastore.folder
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MMail extends MWebfile {
	
	public static $m__sClassName = __CLASS__;
	
	private $m_sFrom;
	private $m_sTo;
	
	private $m_dDate;
	
	private $m_sSubject;
	private $m_lMessage;
	
	private $m_bIsAnswered;
	private $m_bIsDeleted;
	private $m_bIsSeen;
	private $m_bIsDraft;
	
	
	public function getTime() {
		return $this->m_dDate;
	}
	
	public function getGeograficPosition() {
		return NULL;
	}
	
	public function __construct() {
		
	}
	
	public function getFrom() {
		return $this->m_sFrom;
	}
	
	public function setFrom($from) {
		$this->m_sFrom = $from;
	}
	
	public function getTo() {
		return $this->m_sTo;
	}
	
	public function setTo($to) {
		$this->m_sTo = $to;
	}
	
	public function getDate() {
		return $this->m_dDate;
	}
	
	public function setDate($date) {
		$this->m_dDate = $date;
	}
	
	public function getSubject() {
		return $this->m_sSubject;
	}
	
	public function setSubject($subject) {
		$this->m_sSubject = $subject;
	}
	
	public function getMessage() {
		return $this->m_lMessage;
	}
	
	public function setMessage($message) {
		$this->m_lMessage = $message;
	}
	
	public function __toString() {
		return $this->getDate() . "<br /><b>".$this->m_sFrom."</b><br />".$this->m_sSubject."<br /><br />
				<div style=\"text-align:left; width:500px;margin-left: auto ;margin-right: auto ;\">".$this->getMessage()."</div>";
	}
	
}