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
     * Database
     *
     * @var    object  CommonApi\Database\DatabaseInterface
     * @since  1.0
     */
    protected $database = '';

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
     * Connectors
     *
     * @var    array
     * @since  1.0
     */
    protected $connector = array('OR', 'AND');

    /**
     * QueryType
     *
     * @var    array
     * @since  1.0
     */
    protected $query_type_array = array('insert', 'insertfrom', 'select', 'update', 'delete', 'exec');

    /**
     * List of Controller Properties
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array
        = array(
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
        $database_prefix,
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
        if ($this->$key === null) {
            $this->setDefault($key, $default);
        }

        return $this->$key;
    }

    /**
     * Set default - this is to get around Scrutinizer duplicate code silliness
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setDefault($key, $default = null)
    {
        $this->$key = $default;

        return $this;
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

    /**
     * Generate array of column names, values, or name-value pairs
     *
     * @param   array   $input array of objects
     * @param   boolean $get_value
     * @param   boolean $get_column
     * @param   boolean $use_alias
     *
     * @return  string
     * @since   1.0
     */
    protected function getElementsArray(array $input, $get_value = true, $get_column = true, $use_alias = true)
    {
        $array = array();

        foreach ($input as $item) {

            $column_name = '';
            $value       = 0;

            if ($get_column === true) {
                $column_name = $this->setColumnName($item->name);
                $column_name .= $this->setColumnAlias($use_alias, $item->alias);
            }

            if ($get_value === true) {
                $value = $this->setColumnValue($item->name, $item->value, $item->data_type);
            }

            if ($get_value === true && $get_column === true) {
                $array[$column_name] = $value;

            } elseif ($get_value === true) {
                $array[] = $value;

            } else {
                $array[] = $column_name;
            }
        }

        return $array;
    }

    /**
     * Set Column Name
     *
     * @param   string $column_name
     *
     * @return  string
     * @since   1.0
     */
    protected function setColumnName($column_name)
    {
        if (strpos($column_name, '.')) {
            $temp   = explode('.', $column_name);
            $column = $this->quoteNameAndPrefix($temp[1], $temp[0]);
        } else {
            $column = $this->quoteName($column_name);
        }

        return $column;
    }

    /**
     * Set Column Alias
     *
     * @param   boolean $use_alias
     * @param   string  $alias
     *
     * @return  string
     * @since   1.0
     */
    protected function setColumnAlias($use_alias = false, $alias = null)
    {
        if ($alias === null) {
            return '';
        }

        if ($use_alias === false) {
            return '';
        }

        return ' AS ' . $this->quoteName($alias);
    }

    /**
     * Prepare Column Value by filtering and escaping
     *
     * @param   string $column_name
     * @param   mixed  $value
     * @param   string $data_type
     *
     * @return  string
     * @since   1.0
     */
    protected function setColumnValue($column_name, $value, $data_type)
    {
        $value = $this->setOrFilterColumn($column_name, $value, $data_type);

        if (is_numeric($value)) {
            return $value;
        }

        return $this->quoteValue($value);
    }

    /**
     * Edit Array
     *
     * @param   mixed   $array
     * @param   string  $type
     * @param   boolean $exception
     *
     * @return  array
     * @since   1.0
     */
    protected function editArray($array, $type = 'columns', $exception = true)
    {
        if (is_array($array) && count($array) > 0) {
            return $array;
        }

        if ($exception === true) {
            throw new RuntimeException('editArray Method: ' . $type . ' does not have data.');
        }

        return array();
    }

    /**
     * Tests if a required value has been provided
     *
     * @param   string $data_type
     * @param   string $column_name
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function editDataType($data_type = null, $column_name = '')
    {
        if ($data_type === null) {
            throw new RuntimeException(
                'Query-editDataType Method: No Datatype provided for Column: ' . $column_name
            );
        }
    }

    /**
     * Tests if a required value has been provided
     *
     * @param   string $column_name
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function editRequired($column_name, $value = null)
    {
        if (trim($value) === '' || $value === null) {
            throw new RuntimeException('Query: Value required for: ' . $column_name);
        }
    }

    /**
     * Edit Connector
     *
     * @param   string $connector
     *
     * @return  string
     * @since   1.0
     */
    protected function editConnector($connector = null)
    {
        $connector = strtoupper($connector);

        if (in_array($connector, $this->connector)) {
        } else {
            $connector = 'AND';
        }

        return $connector;
    }

    /**
     * Edit WHERE
     *
     * @param   string $left
     * @param   string $right
     * @param   string $condition
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function editWhere($left, $condition, $right)
    {
        if (trim($left) === ''
            || trim($condition) === ''
            || trim($right) === ''
        ) {
            throw new RuntimeException(
                'Query-Where Method: Value required for ' . ' $left: ' . $left
                . ' $condition: ' . $condition . ' $right: ' . $right
            );
        }

        return $this;
    }

    /**
     * Set or Filter Column
     *
     * @param   string $column_name
     * @param   string $value
     * @param   string $filter
     *
     * @return  null|string
     * @since   1.0
     */
    protected function setOrFilterColumn($column_name, $value, $filter)
    {
        if (strtolower($filter) === 'column') {
            return $this->setColumnName($value);
        }

        return $this->filter($column_name, $value, $filter);
    }

    /**
     * Quote Value
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     */
    protected function quoteValue($value)
    {
        return $this->quote_value . $value . $this->quote_value;
    }

    /**
     * Quote Name
     *
     * @param   string $value
     * @param   string $alias
     *
     * @return  string
     * @since   1.0
     */
    protected function quoteNameAndPrefix($value, $prefix = null)
    {
        if ($prefix === null || trim($prefix) === '') {
            $return_prefix = '';

        } else {
            $prefix        = $this->quoteName($prefix);
            $return_prefix = $prefix . '.';
        }

        $value = $this->quoteName($value);

        return $return_prefix . $value;
    }

    /**
     * Quote Name
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     */
    protected function quoteName($value)
    {
        if (trim($value) === '*') {
            return $value;
        }

        return $this->name_quote_start . $value . $this->name_quote_end;
    }

    /**
     * Filter Input
     *
     * @param   string      $key
     * @param   null|string $value
     * @param   string      $data_type
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function filter($key, $value = null, $data_type = 'string')
    {
        try {
            $results = $this->fieldhandler->sanitize($key, $value, ucfirst(strtolower($data_type)));

            $value = $results->getFieldValue();

        } catch (Exception $e) {
            throw new RuntimeException(
                'Request: Filter class Failed for Key: ' . $key . ' Filter: ' . $data_type . ' ' . $e->getMessage()
            );
        }

        return $this->quoteValue($value);
    }
}
