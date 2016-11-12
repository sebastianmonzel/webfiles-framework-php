<?php

namespace simpleserv\webfilesframework\core\authentication;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MUser extends MWebfile
{

    protected $m_sUsername;
    protected $m_sPasswordHash;
    protected $m_sPasswordSalt;

    public static $m__sClassName = __CLASS__;

    public function __construct($name = null)
    {
        $this->m_sUsername = $name;
    }

    public function getUsername()
    {
        return $this->m_sUsername;
    }

    public function setUsername($username)
    {
        $this->m_sUsername = $username;
    }

    public function getPasswordHash()
    {
        return $this->m_sPasswordHash;
    }

    public function setPasswortHash($passwordHash)
    {
        $this->m_sPasswordHash = $passwordHash;
    }

    public function getPasswordSalt()
    {
        return $this->m_sPasswordSalt;
    }

    public function setPasswordSalt($passwordSalt)
    {
        $this->m_sPasswordSalt = $passwordSalt;
    }

}