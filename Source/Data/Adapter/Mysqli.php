<?php
/**
 * Mysqli Database Adapter
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Data\Adapter;

use CommonApi\Query\ConnectionInterface;
use CommonApi\Query\DatabaseInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Mysqli Database Adapter
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class Mysqli extends MysqliBase implements DatabaseInterface, ConnectionInterface
{
    /**
     * Constructor
     *
     * @param  array $options
     *
     * @since  1.0
     */
    public function __construct($options)
    {
        if (count($options) > 0) {
            $this->setOptions($options);
        }
    }

    /**
     * Set the Database Object
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0.0
     */
    public function connect(array $options = array())
    {
        if (count($options) > 0) {
            $this->setOptions($options);
        }

        $this->isSupported();

        $this->connectDatabaseServer();

        return $this;
    }

    /**
     * Escape the value
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0.0
     */
    public function escape($value)
    {
        $this->checkConnected();

        $value = mysqli_real_escape_string($this->database, $value);

        if (is_numeric($value)) {
            return $value;
        }

        return '"' . $value . '"';
    }

    /**
     * Escape the name value
     *
     * @param   string $name
     *
     * @return  string
     * @since   1.0.0
     */
    public function escapeName($name)
    {
        return $this->name_quote_start . trim($name) . $this->name_quote_end;
    }

    /**
     * Query the database and return a single value as the result
     *
     * @param   string $sql
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function loadResult($sql)
    {
        $cursor = $this->loadDatabaseCursor($sql);

        if (is_object($cursor)) {
        } else {
            return null;
        }

        return $this->loadResultValue($cursor);
    }

    /**
     * Query the database and return an array of object values returned from query
     *
     * @param   string $sql
     *
     * @return  array
     * @since   1.0.0
     */
    public function loadObjectList($sql)
    {
        $cursor = $this->loadDatabaseCursor($sql);

        if ($cursor === false) {
            return null;
        }

        return $this->loadObjectListRows($cursor);
    }

    /**
     * Execute the Database Query (SQL can be sent in or derived from Query Object)
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0.0
     */
    public function execute($sql)
    {
        $this->checkConnected();

        $cursor = mysqli_query($this->database, $sql);

        if ($cursor) {
        } else {
            throw new RuntimeException(
                (string)mysqli_error($this->database)
                . "\n-- SQL --\n"
                . $sql,
                (int)mysqli_errno($this->database)
            );
        }

        return $cursor;
    }

    /**
     * Returns the primary key following insert
     *
     * @return  integer
     * @since   1.0.0
     */
    public function getInsertId()
    {
        $this->checkConnected();

        return mysqli_insert_id($this->database);
    }

    /**
     * Disconnect from Database
     *
     * @return  $this
     * @since   1.0.0
     */
    public function disconnect()
    {
        if ($this->checkConnectedTest() === true) {
            $this->database = null;
        }

        return $this;
    }
}
