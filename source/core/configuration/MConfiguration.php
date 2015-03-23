<?php

namespace \simpleserv\webfiles-framework\core\configuration;

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
 * @package    de.simpleserv.core.configuration
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MConfiguration {

    private $m_oRegistry;

    static private $instance = null;

    static public function getInstance()
    {
        if (null === self::$instance) {
            
            self::$instance = new self;
            
        }
        return self::$instance;
    }


    public function getValue($p_sKey)
    {
        if ( array_key_exists($p_sKey,$this->m_oRegistry) ) {
            return $this->m_oRegistry[$p_sKey];
        } else {
            return -1;
        }
    }

    private function __construct(){
        global $config;
        $this->m_oRegistry = $config;
    }

    private function __clone(){}

}