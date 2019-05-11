<?php

namespace simpleserv\webfilesframework\core\datastore\types\mail;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Defines the account information of a given imap mail account.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MMailAccount extends MWebfile
{

    public static $m__sClassName = __CLASS__;

    /** @var string $m_sHost */
    private $m_sHost;
    /** @var string $m_sPort */
    private $m_sPort;
    /** @var string $m_sUser */
    private $m_sUser;
    /** @var string $m_sPassword */
    private $m_sPassword;

    public function __construct($host, $port, $user, $password)
    {

        $this->m_sHost = $host;
        $this->m_sPort = $port;
        $this->m_sUser = $user;
        $this->m_sPassword = $password;
    }

    public function getHost()
    {
        return $this->m_sHost;
    }

    public function getPort()
    {
        return $this->m_sPort;
    }

    public function getUser()
    {
        return $this->m_sUser;
    }

    public function getPassword()
    {
        return $this->m_sPassword;
    }

}