<?php
/**
 * Abstract Query Adapter
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Adapter;

use CommonApi\Database\DatabaseInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Model\FieldhandlerInterface;
use CommonApi\Query\QueryInterface;
use DateTime;
use Exception;
use stdClass;

/**
 * Abstract Query Adapter
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class AbstractAdapter implements QueryInterface
{
    /**
     * Fieldhandler Instance
     *
     * @var    object  CommonApi\Query\FieldhandlerInterface
     * @since  1.0
     */
    protected $fieldhandler = '';

    /**
     * Database Prefix
     *
     * @var    string
     * @since  1.0
     */
    protected $database_prefix = '';

    /**
     * Query Type
     *
     * @var    string
     * @since  1.0
     */
    protected $query_type = 'select';

    /**
     * Distinct
     *
     * @var    boolean
     * @since  1.0
     */
    protected $distinct = false;

    /**
     * Columns
     *
     * @var    array
     * @since  1.0
     */
    protected $columns = array();

    /**
     * Values
     *
     * @var    array
     * @since  1.0
     */
    protected $values = array();

    /**
     * From
     *
     * @var    array
     * @since  1.0
     */
    protected $from = array();

    /**
     * Where Group
     *
     * @var    array
     * @since  1.0
     */
    protected $where_group = array();

    /**
     * Where
     *
     * @var    array
     * @since  1.0
     */
    protected $where = array();

    /**
     * Group By
     *
     * @var    array
     * @since  1.0
     */
    protected $group_by = array();

    /**
     * Having Group
     *
     * @var    array
     * @since  1.0
     */
    protected $having_group = array();

    /**
     * Having
     *
     * @var    array
     * @since  1.0
     */
    protected $having = array();

    /**
     * Order By
     *
     * @var    array
     * @since  1.0
     */
    protected $order_by = array();

    /**
     * Offset
     *
     * @var    int
     * @since  1.0
     */
    protected $offset = 0;

    /**
     * Limit
     *
     * @var    int
     * @since  1.0
     */
    protected $limit = 0;

    /**
     * SQL
     *
     * @var    string
     * @since  1.0
     */
    protected $sql = '';

    /**
     * Date Format
     *
     * @var    string
     * @since  1.0
     */
    protected $date_format = '';

    /**
     * Null Date
     *
     * @var    string
     * @since  1.0
     */
    protected $null_date = '';

    /**
     * Name quote start
     *
     * @var    string
     * @since  1.0
     */
    protected $name_quote_start = '"';

    /**
     * Name quote start
     *
     * @var    string
     * @since  1.0
     */
    protected $name_quote_end = '"';

    /**
     * Current Date
     *
     * @var    string
     * @since  1.0
     */
    protected $quote_value = '"';

    /**
     * List of Controller Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'database_prefix',
        'query_type',
        'distinct',
        'columns',
        'values',
        'from',
        'where_group',
        'where',
        'group_by',
        'having_group',
        'having',
        'order_by',
        'offset',
        'limit',
        'date_format',
        'null_date'
    );

    /**
     * Constructor
     *
     * @since  1.0
     */
    public function __construct(
        FieldhandlerInterface $fieldhandler,
        $database_prefix = '',
        DatabaseInterface $database
    ) {
        $this->fieldhandler    = $fieldhandler;
        $this->database_prefix = $database_prefix;
        $this->database        = $database;

        $this->clearQuery();
    }

    /**
     * Get the current value (or default) of the specified property
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function get($key, $default = null)
    {
        if (in_array($key, $this->property_array)) {
        } else {
            throw new RuntimeException('Controller Get: Unknown key: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Clear Query String
     *
     * @return  $this
     * @since   1.0
     */
    public function clearQuery()
    {
        $this->query_type = 'select';
        $this->distinct   = false;
        $this->columns    = array();
        $this->values     = array();
        $this->from       = array();
        $this->where      = array();
        $this->group_by   = array();
        $this->having     = array();
        $this->order_by   = array();
        $this->offset     = 0;
        $this->limit      = 0;
        $this->sql        = '';

        return $this;
    }

    /**
     * Retrieves the PHP date format compliant with the database driver
     *
     * @return  string
     * @since   1.0
     */
    public function getDateFormat()
    {
        return $this->date_format;
    }

    /**
     * Retrieves the current date and time formatted in a manner compliant with the database driver
     *
     * @return  string
     * @since   1.0
     */
    public function getDate()
    {
        $date = new DateTime();

        return $date->format($this->date_format);
    }

    /**
     * Returns a value for null date that is compliant with the database driver
     *
     * @return  string
     * @since   1.0
     */
    public function getNullDate()
    {
        return $this->null_date;
    }
}
