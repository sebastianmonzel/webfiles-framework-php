<?php

namespace simpleserv\webfilesframework\core\datasystem\database;

use simpleserv\webfilesframework\MItem;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatabaseConnection extends MItem
{
    private $host = '127.0.0.1';

    private $database = 'webfiles';
    private $tablePrefix = 'default_';

    private $username = 'root';
    private $password = '';

    /** @var \mysqli $connection */
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
    public function __construct(
        $host = null,
        $database = null,
        $tablePrefix = null,
        $username = null,
        $password = null)
    {

        if ($host != null) {
            $this->host = $host;
        }
        if ($database != null) {
            $this->database = $database;
        }
        if ($tablePrefix != null) {
            $this->tablePrefix = $tablePrefix;
        }
        if ($username != null) {
            $this->username = $username;
        }
        if ($password != null) {
            $this->password = $password;
        }

        $this->connect();
    }

    /**
     * multiton to administrate the instances of the connection
     * and global accessing
     *
     * @param String $instanceName
     * @return object
     */
    static public function getInstance($instanceName)
    {
        if (!isset(self::$instanceArray[$instanceName])) {
            self::$instanceArray[$instanceName] = new MDatabaseConnection();
        }
        return self::$instanceArray[$instanceName];
    }

    /**
     * connects to the database server
     */
    public function connect()
    {
        $this->connection = @new \mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if (!$this->connection->connect_errno) {
            $this->connection->autocommit(1);
            return true;
        } else {
            return false;
        }
    }

    /**
     * closes the connection
     */
    public function close()
    {
        @$this->connection->close();
    }

    /**
     * queries the database with the specified sql command
     *
     * @param $sqlCommand
     * @return bool|\mysqli_result
     * @internal param String $p_sSqlCommand
     */
    public function query($sqlCommand)
    {
        return $this->connection->query($sqlCommand);
    }

    /**
     * prints last error of the connection
     */
    public function getError()
    {
        return $this->connection->error;
    }

    /**
     * returns id of a last insert query
     *
     * @return int
     */
    public function getInsertId()
    {
        return $this->connection->insert_id;
    }

    /**
     * returns the hostname of the databaseserver
     *
     * @access public
     * @return String
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * returns the name of the user connected to the database server
     *
     * @access public
     * @return String
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return null|string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Sets the database for the given connection
     * @access public
     * @param $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * Returns the table prefix used for the given
     * connection.
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

}
