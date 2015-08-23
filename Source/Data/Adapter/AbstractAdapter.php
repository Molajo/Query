<?php
/**
 * Abstract Adapter Database Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Data\Adapter;

use CommonApi\Query\DatabaseInterface;
use CommonApi\Query\ConnectionInterface;

/**
 * Database Connection
 *
 * @package     Molajo
 * @subpackage  Database
 * @since       1.0.0
 */
abstract class AbstractAdapter implements DatabaseInterface, ConnectionInterface
{
    /**
     * Database Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $database_type;

    /**
     * Options
     *
     * @var    array
     * @since  1.0.0
     */
    protected $options;

    /**
     * Database Instance
     *
     * @var    object
     * @since  1.0.0
     */
    protected $database;

    /**
     * DB Options
     *
     * @var    array
     * @since  1.0.0
     */
    protected $db_option_keys
        = array(
            'host'     => FILTER_SANITIZE_STRING,
            'port'     => FILTER_SANITIZE_NUMBER_INT,
            'socket'   => FILTER_SANITIZE_STRING,
            'user'     => FILTER_SANITIZE_STRING,
            'password' => FILTER_SANITIZE_STRING,
            'database' => FILTER_SANITIZE_STRING,
            'prefix'   => FILTER_SANITIZE_STRING
        );

    /**
     * Connect to the Database, passing in credentials and other data needed
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0.0
     */
    abstract public function connect(array $options = array());

    /**
     * Disconnects from Database and removes the database connection, freeing resources
     *
     * @return  $this
     * @since   1.0.0
     */
    abstract public function disconnect();

    /**
     * Escape the value
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0.0
     */
    abstract public function escape($value);

    /**
     * Escape the name value
     *
     * @param   string $name
     *
     * @return  string
     * @since   1.0.0
     */
    abstract public function escapeName($name);

    /**
     * Query the database and return a single value as the result
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0.0
     */
    abstract public function loadResult($sql);

    /**
     * Query the database and return an array of object values returned from query
     *
     * @param   string $sql
     *
     * @return  array
     * @since   1.0.0
     */
    abstract public function loadObjectList($sql);

    /**
     * Execute the Database Query
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0.0
     */
    abstract public function execute($sql);

    /**
     * Returns the primary key following insert
     *
     * @return  integer
     * @since   1.0.0
     */
    abstract public function getInsertId();
}
