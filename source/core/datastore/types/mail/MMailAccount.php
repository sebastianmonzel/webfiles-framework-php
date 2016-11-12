<?php

namespace simpleserv\webfilesframework\core\datastore\types\mail;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Defines the account information of a given imap mail account.
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MMailAccount extends MWebfile
{

    public static $m__sClassName = __CLASS__;

    private $m_sHost;
    private $m_sPort;
    private $m_sUser;
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