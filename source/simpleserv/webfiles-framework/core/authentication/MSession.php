<?php

namespace \simpleserv\webfiles-framework\core\authentication;

/**
 * 
 * Enter description here ...
 * @author Sebastian Monzel, <mail@sebastianmonzel.de>
 *
 */
class MSession {
	
	private static $instance = null; 
	
	private $username;
	private $passwordHash;
	
	private $formHash;
	private $lastFormHashTime = 0;
	
	private $lastActionTime = 0;
	
	private $sessionInitializer;
	
	/**
	 * 
	 * Enter description here ...
	 * @return MSession
	 */
	public static function getInstance() {
		if ( null === self::$instance ) {
			self::$instance = new MSession();			
		}
		
		return self::$instance;
	}
	
	public function init() {
		session_start();
		
		$this->restore();
		
		if ( $this->checkActualSystemIsValidWithCoupling() ) {
			$this->regenerateFormHash(false);
		} else {
			//SESSION PROBABLY HIJACKED - GENERATE NEW HASHES AND SESSION ID / DELETE SESSION FILE
			session_regenerate_id(true);
			$this->regenerateFormHash(true);
		}
		$this->makeSystemCoupling();
	}
	
	private function generateFormHash() {
		$this->lastFormHashTime = microtime(true);
		$this->formHash = md5($this->lastFormHashTime);
		
		$this->store();
	}
	
	public function regenerateFormHash($forced = false) {
		
		$secondsBeforeRefresh = 10 * 60;
		//$secondsBeforeRefresh = 20; // 20 seconds for testing porpuses
		
		if ( $forced || ( (microtime(true) - $this->lastFormHashTime) > $secondsBeforeRefresh ) ) {
			$this->generateFormHash();
		}
	}
	
	public function login(MUser $referenceObject,$username, $password) {
		
		$webfiles = MSite::getInstance()->getDefaultDatastore()->getByTemplate($referenceObject);
		
		if ( count($webfiles) != 1 ) {
			return false;
		}
		
		
		
		$user = $webfiles[0];
		$passwordHash = crypt($password,$user->getPasswordSalt());
		
		//echo $passwordHash . "<br />";
		//echo $user->getPasswordHash();
		
		
		if ( $passwordHash == $user->getPasswordHash() ) {
			
			$this->setValue('current_user', $username);
			$this->setValue('current_password_hash',$passwordHash);
			$this->setValue('current_user_object',$referenceObject->marshall());
			
			$this->sessionInitializer->initializeByUserObject($user);
			
			return true;
		}
		return false;
	}
	
	public function destroy() {
		unset($_SESSION['current_user']);
		unset($_SESSION['current_password_hash']);
		
		return session_destroy();
	}
	
	public function isStarted() {
		return ( session_id() != "" );
	}
	
	private function isCoupledWithSystem() {
		return $this->hasValue("_ident_ip");
	}
	
	private function makeSystemCoupling() {
		$ip = getenv("REMOTE_ADDR");
		$this->setValue("_ident_ip",$ip);
	}
	
	private function checkActualSystemIsValidWithCoupling() {
		return ($this->getValue("_ident_ip") == getenv("REMOTE_ADDR"));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function isValid() {
		if ( ! $this->hasValue('current_user') || ! $this->hasValue('current_password_hash') ) {
			return false;
		}
		
		/*$user = new MUser($_SESSION['current_user']);
		
		if ( ! $user->load() ) {
			return false;
		}
		
		if ( $this->getValue('current_password_hash') != $user->getPasswordHash() ) {
			return false;
		}	*/
		return true;
	}
	
	public function getFormHash() {
		return $this->formHash;
	}
	
	public function store() {
		$this->setValue('formHash', $this->formHash);
		$this->setValue('lastFormHashTime', $this->lastFormHashTime);
	}
	
	public function restore() {
		if ( $this->hasValue('formHash') ) {
			$this->formHash = $this->getValue('formHash');
		}
		if ( $this->hasValue('lastFormHashTime') ) {
			$this->lastFormHashTime = $this->getValue('lastFormHashTime');
		}
		
		if ( $this->hasValue('current_user') ) {
			$this->username = $this->getValue('current_user');
		}
		
		if ( $this->hasValue('current_password_hash') ) {
			$this->passwordHash = $this->getValue('current_password_hash');
		}
		
	}
	
	public function setValue($name, $value) {
		$_SESSION[$name] = $value;
	}
	
	public function hasValue($name) {
		return isset($_SESSION[$name]);
	}
	
	public function getValue($name) {
		if ( isset($_SESSION[$name]) ) {		
			return $_SESSION[$name];
		} else {
			return null;
		}
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function getIp() {
		return $this->getValue("_ident_ip");
	}
	
	
	public function setSessionInitializer(MAbstractSessionInitializer $sessionInitializer) {
		$this->sessionInitializer = $sessionInitializer;
	}
	
}