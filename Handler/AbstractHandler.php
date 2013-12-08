<?php
/**
 * Abstract Handler Query Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Handler;

use CommonApi\Query\ConnectionInterface;
use CommonApi\Query\QueryInterface;
use CommonApi\Query\QueryInterface;
use Exception\Query\AbstractHandlerException;

/**
 * Query Connection
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
abstract class AbstractHandler implements ConnectionInterface, QueryInterface, QueryInterface
{
    /**
     * Query Type
     *
     * @var    string
     * @since  1.0
     */
    protected $query_type;

    /**
     * Options
     *
     * @var    array
     * @since  1.0
     */
    protected $options;

    /**
     * Query Instance
     *
     * @var    object
     * @since  1.0
     */
    protected $query;

    /**
     * Query Object Instance
     *
     * @var    object
     * @since  1.0
     */
    protected $query;

    /**
     * Null Date for Query
     *
     * @var    object
     * @since  1.0
     */
    protected $null_date;

    /**
     * Date Format
     *
     * @var    object
     * @since  1.0
     */
    protected $date_format;

    /**
     * Constructor
     *
     * @param   array $options
     *
     * @return  mixed
     * @since   1.0
     */
    abstract public function __construct(array $options = array());

    /**
     * Set the Query Object
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function connect($options = array());

    /**
     * Get the current query object for the current query connection
     *
     * @return  object
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function getQueryObject();

    /**
     * Returns a query driver compliant date format for PHP date()
     *
     * @return  string The format string.
     * @since   1.0
     */
    abstract public function getDateFormat();

    /**
     * Retrieves the current date and time formatted compliant with the query driver
     *
     *  $date_format = $adapter->getDate();
     *
     * @return  string
     * @since   1.0
     */
    abstract public function getDate();

    /**
     * Returns a query driver compliant value for null date
     *
     * @return  string
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function getNullDate();

    /**
     * Quote Value
     *
     * @param   string $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function quote($value);

    /**
     * Quote and return name
     *
     * @param   string $name
     *
     * @return  string
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function quoteName($name);

    /**
     * Method to escape a string for usage in an SQL statement.
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function escape($value);

    /**
     * Returns a string containing the query, resolved from the query object
     *
     * @return  string
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function getQueryString();

    /**
     * Single Value Query Result returned
     *
     * @return  object
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function loadResult();

    /**
     * Array of object values returned from query
     *
     * @param   null|int $offset
     * @param   null|int $limit
     *
     * @return  object
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function loadObjectList($offset = null, $limit = null);

    /**
     * Execute the Query Query (SQL can be sent in or derived from Query Object)
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function execute($sql = null);

    /**
     * Returns the primary key following insert
     *
     * @return  integer
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function getInsertId();

    /**
     * Disconnect from Query
     *
     * @return  $this
     * @since   1.0
     * @throws  AbstractHandlerException
     */
    abstract public function disconnect();
}
