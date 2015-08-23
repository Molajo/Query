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
use Exception;

/**
 * Mysqli Database Adapter
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class MysqliBase extends AbstractAdapter
{
    /**
     * Database Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $database_type = 'Mysqli';

    /**
     * Date Format
     *
     * @var    string
     * @since  1.0.0
     */
    protected $date_format = 'Y-m-d H:i:s';

    /**
     * Null Date
     *
     * @var    string
     * @since  1.0.0
     */
    protected $null_date = '0000-00-00 00:00:00';

    /**
     * Name quote start
     *
     * @var    string
     * @since  1.0.0
     */
    protected $name_quote_start = '`';

    /**
     * Name quote end
     *
     * @var    string
     * @since  1.0.0
     */
    protected $name_quote_end = '`';

    /**
     * Quote Value
     *
     * @var    string
     * @since  1.0.0
     */
    protected $quote_value = '\'';

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
     * Filter and set DB Connection options
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setOptions(array $options = array())
    {
        foreach ($this->db_option_keys as $key => $filter) {
            $this->options[$key] = $this->setOptionValue($key, $filter, $options);
        }

        if ((int)$this->options['port'] === 0) {
            $this->options['port'] = 3306;
        }

        return $this;
    }

    /**
     * Set and filter option value
     *
     * @param   string $key
     * @param   string $filter
     * @param   array  $options
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function setOptionValue($key, $filter, array $options = array())
    {
        $unfiltered_value = null;

        if (isset($options[$key])) {
            $unfiltered_value = $options[$key];
        }

        return filter_var($unfiltered_value, $filter);
    }

    /**
     * Is this database supported?
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function isSupported()
    {
        if (function_exists('mysqli_connect') === true) {
            return $this;
        }

        throw new RuntimeException('PHP Mysqli extension is not available');
    }

    /**
     * Check if the database is still connected, if not try to reconnect
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function checkConnected()
    {
        if ($this->checkConnectedTest() === true) {
            return $this;
        }

        $this->connectDatabaseServer();

        if ($this->checkConnectedTest() === true) {
            return $this;
        }

        throw new RuntimeException('PHP Database Connection is not available.');
    }

    /**
     * Check if the database is still connected, if not try to reconnect
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function checkConnectedTest()
    {
        if (isset($this->database->connect_errno)
            && $this->database->connect_errno === 0
        ) {
            return true;
        }

        return false;
    }

    /**
     * Connect to Database Server
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function connectDatabaseServer()
    {
        try {

            $connect = mysqli_connect(
                $this->options['host'],
                $this->options['user'],
                $this->options['password'],
                $this->options['database'],
                $this->options['port']
            );

        } catch (Exception $e) {
            throw new RuntimeException('Mysqli Database Adapter connectDatabaseServer Exception: ' . $e->getMessage());
        }

        $this->database = $connect;

        return $this;
    }

    /**
     * Set Database Query Cursor
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0.0
     */
    public function loadDatabaseCursor($sql)
    {
        $this->checkConnected();

        return mysqli_query($this->database, $sql);
    }

    /**
     * Load the Result Value
     *
     * @param   object $cursor
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function loadResultValue($cursor)
    {
        $row = mysqli_fetch_assoc($cursor);

        $value = null;

        if (is_array($row) && count($row) > 0) {
            foreach ($row as $key => $value) {
                break;
            }
        }

        mysqli_free_result($cursor);

        return $value;
    }

    /**
     * Set Object List Cursor
     *
     * @param   object $cursor
     *
     * @return  array
     * @since   1.0.0
     */
    public function loadObjectListRows($cursor)
    {
        $results = array();

        while (1 === 1) {

            $row = mysqli_fetch_object($cursor, '\\stdClass');

            if ($row === null) {
                break;
            }

            $results[] = $row;
        }

        mysqli_free_result($cursor);

        return $results;
    }

    /**
     * Class Destructor
     *
     * @return  $this
     * @since   1.0.0
     */
    public function __destruct()
    {
        return $this->disconnect();
    }
}
