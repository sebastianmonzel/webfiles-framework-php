<?php

namespace simpleserv\webfilesframework\core\datasystem\database;

use simpleserv\webfilesframework\MItem;

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
 * @package    de.simpleserv.core.database
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MDatabaseConnection extends MItem {
    private $host = '127.0.0.1';
	
    private $database = 'webfiles';
    private $tablePrefix = 'default_';
    
    private $username = 'root';
    private $password = '';
    
    private $connection;
    
    static private $instanceArray = array();
    
    /**
     * 
     * @param string $host the host to connect to.
     * @param string $database the database to connect to.
     * @param string $tablePrefix the table prefix used for the actual connection to the database.
     * @param string $username the username for the connection.
     * @param string $password the password for the connection.
     */
	public function __construct($host = null, $database = null, $tablePrefix = null , $username = null, $password = null ) {
		
		if ( $host != null ) {
			$this->host = $host;
		}
		if ( $database != null ) {
			$this->database = $database;
		}
		if ( $tablePrefix != null ) {
			$this->tablePrefix = $tablePrefix;
		}
		if ( $username != null ) {
			$this->username = $username;
		}
		if ( $password != null ) {
			$this->password = $password;
		} 
		
		$this->connect();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function __destruct() {
		$this->close();
	}
	
	/**
	 * multiton to administrate the instances of the connection
	 * and global accessing
	 *
	 * @param String $instanceName
	 * @return object
	 */
    static public function getInstance($instanceName) {
    	if (!isset(self::$instanceArray[$instanceName])) {
    		self::$instanceArray[$instanceName] = new MDatabaseConnection();
    	}
    	return self::$instanceArray[$instanceName];
    }
	
    /**
     * connects to the database server
     */
    public function connect() {
    	$this->connection = @new mysqli(
    			$this->host,
    			$this->username,
    			$this->password,
    			$this->database
    	);
    	
    	if (! $this->connection->connect_errno) {
    		$this->connection->autocommit(1);
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * closes the connection
     */
    public function close() {
    	@$this->connection->close();
    }
    
    /**
     * queries the database with the specified sql command
     *
     * @param String $p_sSqlCommand
     * @return unknown
     */
	public function query($p_sSqlCommand) {
    	return $this->connection->query($p_sSqlCommand);
    }
    
    /**
     * prints last error of the connection
     */
    public function getError() {
    	return $this->connection->error;
    }
    
    /**
     * returns id of a last insert query 
     *
     * @return int
     */
    public function getInsertId() {
    	return $this->connection->insert_id;
    }
    
    /**
     * returns the hostname of the databaseserver
     *
     * @access public
     * @return String
     */
    public function getHost() {
        return $this->host;
    }
	
    /**
     * sets the hostname of the databaseserver
     *
     * @access public
     * @return void
     */
    public function setHost($host) {
        $this->host = $host;
    }
    

    /**
     * returns the name of the user connected to the database server
     *
     * @access public
     * @return String
     */
    public function getUser() {
        return $this->user;
    }
	
    /**
     * sets the name of the user connected to the database server
     *
     * @access public
     * @return void
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * returns the password of the database connection
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * sets the password of the database connection
     *
     * @access public
     * @return void
     */
    public function setPassword($password) {
        $this->password = $password;
    }
    
	/**
     *
     * @access public
     * @return void
     */
    public function getDatabase() {
        return $this->database;
    }

    /**
     *
     * @access public
     * @return void
     */
    public function setDatabase($database) {
        $this->database = $database;
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function getTablePrefix() {
    	return $this->tablePrefix;
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $tablePrefix
     */
	public function setTablePrefix($tablePrefix) {
    	$this->tablePrefix = $tablePrefix;
    }
    
}
