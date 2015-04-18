<?php

namespace simpleserv\webfilesframework\core\authentication;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
/**
 * 
 * @author semo
 *
 */
class MUser extends MWebfile {
	
	protected $m_sUsername;
	protected $m_sPasswordHash;
	protected $m_sPasswordSalt;
	
	public static $m__sClassName = __CLASS__;
	
	public function __construct($name) {
		$this->name = $name;
	}
	
	public function getUsername() {
		return $this->m_sUsername;
	}
	
	public function setUsername($username) {
		$this->m_sUsername = $username;
	}
	
	public function getPasswordHash() {
		return $this->m_sPasswordHash;
	}
	
	public function setPasswortHash($passwordHash) {
		$this->m_sPasswordHash = $passwordHash;
	}
	
	public function getPasswordSalt() {
		return $this->m_sPasswordSalt;
	}
	
	public function setPasswortSalt($passwordSalt) {
		$this->m_sPasswordSalt = $passwordSalt;
	}
	
}