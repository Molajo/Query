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
     * Set Query Type
     *
     * @param   string $query_type
     *
     * @return  $this
     * @since   1.0
     */
    public function setType($query_type = 'select')
    {
        $query_type = strtolower($query_type);

        if ($query_type == 'insert'
            || $query_type == 'insert-from'
            || $query_type == 'select'
            || $query_type == 'update'
            || $query_type == 'delete'
            || $query_type == 'exec'
        ) {
            $this->query_type = $query_type;
        } else {
            $this->query_type = 'select';
        }

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
     * Set Distinct Indicator
     *
     * @param   boolean $distinct
     *
     * @return  $this
     * @since   1.0
     */
    public function setDistinct($distinct = false)
    {
        if ($distinct === true) {
            $this->distinct = true;
        } else {
            $this->distinct = false;
        }

        return $this;
    }

    /**
     * Used for select, insert, and update to specify column name, alias (optional)
     *  For Insert and Update, only, value and data_type
     *
     * @param   string      $column_name
     * @param   null|string $alias
     * @param   null|string $value
     * @param   null|string $data_type
     *
     * @return  $this
     * @since   1.0
     * @throws \CommonApi\Exception\RuntimeException
     */
    public function select($column_name, $alias = null, $value = null, $data_type = null)
    {
        if (trim($column_name) == '') {
            throw new RuntimeException ('Query-Select Method: Value required for $column_name.');
        }

        if ($data_type === 'special') {
            $column = $column_name;
        } else {
            $column = $this->setColumnName($column_name);
        }

        $item            = new stdClass();
        $item->column    = $column;

        if ($alias === null || trim($alias) == '') {
            $item->alias = null;
        } else {
            $item->alias     = $this->quoteName($alias);
        }

        $item->value     = $value;
        $item->data_type = $data_type;

        if ($data_type === null || trim($data_type) == '') {
            $item->data_type = null;
            $item->value     = null;

        } elseif ($data_type === 'special') {
            $item->data_type = $data_type;
            $item->value     = $value;

        } else {
            $item->data_type = $data_type;
            $item->value     = $this->filter($item->column, $value, $item->data_type);
        }

        $this->columns[$column_name] = $item;

        return $this;
    }

    /**
     * Set From table name and optional value for alias
     *
     * @param   string      $table_name
     * @param   null|string $alias
     *
     * @return  $this
     * @since   1.0
     * @throws \CommonApi\Exception\RuntimeException
     */
    public function from($table_name, $alias = null)
    {
        if (trim($table_name) == '') {
            throw new RuntimeException ('Query-From Method: Value required for $table_name.');
        }

        $table = $this->quoteName($table_name);

        if ($alias === null || trim($alias) == '') {
        } else {
            $table .= ' AS ' . $this->quoteName($alias);
        }

        $this->from[] = $table;

        return $this;
    }

    /**
     * Create a grouping for conditions for 'and' or 'or' treatment between groups of conditions
     *
     * @param   string $group
     * @param   string $group_connector
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function whereGroup($group, $group_connector = 'AND')
    {
        if ($group === null || trim($group) == '') {
            throw new RuntimeException
            ('Query Adapter WhereGroup Method Exception');
        }

        $group_connector = strtoupper($group_connector);

        if ($group_connector == 'OR') {
            $group_connector = 'OR';
        } else {
            $group_connector = 'AND';
        }

        $this->where_group[$group] = $group_connector;

        return $this;
    }

    /**
     * Set Where Conditions for Query
     *
     * @param   string      $left_filter
     * @param   string      $left
     * @param   string      $condition
     * @param   string      $right_filter
     * @param   string      $right
     * @param   string      $connector
     * @param   null|string $group
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function where(
        $left_filter = 'column',
        $left,
        $condition,
        $right_filter = 'column',
        $right,
        $connector = 'AND',
        $group = null
    ) {
        if (trim($left_filter) == ''
            || trim($condition) == ''
            || trim($right_filter) == ''
        ) {
            throw new RuntimeException
            ('Query-Where Method: Value required for '
            . ' $left_filter: ' . $left_filter
            . ' $left: ' . $left
            . ' $condition: ' . $condition
            . ' $right_filter: ' . $right_filter
            . ' $right: ' . $right);
        }

        if ($group === null) {
            $group = '';
        }

        $connector = strtoupper($connector);

        if ($connector == 'OR') {
            $connector = 'OR';
        } else {
            $connector = 'AND';
        }

        if (strtolower($left_filter) == 'column') {
            $left = $this->setColumnName($left);
        } else {
            $left = $this->filter('Left', $left, $left_filter);
        }

        if (strtolower($right_filter) == 'column') {
            $right = $this->setColumnName($right);
        } else {
            if (strtolower($condition) == 'in') {
                $right = $this->processInArray('Right', $right, $right_filter);
            } else {
                $right = $this->filter('Right', $right, $right_filter);
            }
        }

        $item            = new stdClass();
        $item->left      = $left;
        $item->condition = $condition;
        $item->right     = $right;
        $item->connector = $connector;
        $item->group     = $group;
        $this->where[]   = $item;

        return $this;
    }

    /**
     * Group By column name and optional value for alias
     *
     * @param   string      $column_name
     * @param   null|string $alias
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function groupBy($column_name, $alias = null)
    {
        if (trim($column_name) == '') {
            throw new RuntimeException ('Query-Group By Method: Value required for $column_name.');
        }

        $column = $this->setColumnName($column_name);

        if ($alias === null || trim($alias) == '') {
        } else {
            $column = $this->quoteName($alias) . '.' . $column;
        }

        $this->group_by[] = $column;

        return $this;
    }

    /**
     * Create a grouping for having statements for 'and' or 'or' treatment between groups of conditions
     *
     * @param   string $group
     * @param   string $group_connector
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function havingGroup($group, $group_connector = 'AND')
    {
        if ($group === null || trim($group) == '') {
            throw new RuntimeException
            ('Query Adapter WhereGroup Method Exception');
        }

        $group_connector = strtoupper($group_connector);

        if ($group_connector == 'OR') {
            $group_connector = 'OR';
        } else {
            $group_connector = 'AND';
        }

        $this->having_group[$group] = $group_connector;

        return $this;
    }

    /**
     * Set Having Conditions for Query
     *
     * @param   string      $left_filter
     * @param   string      $left
     * @param   string      $condition
     * @param   string      $right_filter
     * @param   string      $right
     * @param   string      $connector
     * @param   string|null $group
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function having(
        $left_filter = 'column',
        $left,
        $condition,
        $right_filter = 'column',
        $right,
        $connector = 'AND',
        $group = null
    ) {
        if (trim($left_filter) == ''
            || trim($condition) == ''
            || trim($right_filter) == ''
        ) {
            throw new RuntimeException
            ('Query-Having Method: Value required for '
            . ' $left_filter: ' . $left_filter
            . ' $left: ' . $left
            . ' $condition: ' . $condition
            . ' $right_filter: ' . $right_filter
            . ' $right: ' . $right);
        }

        if ($group === null) {
            $group = '';
        }

        $connector = strtoupper($connector);

        if ($connector == 'OR') {
            $connector = 'OR';
        } else {
            $connector = 'AND';
        }

        if (strtolower($left_filter) == 'column') {
            $left = $this->setColumnName($left);
        } else {
            $left = $this->filter('Left', $left, $left_filter);
        }

        if (strtolower($left_filter) == 'column') {
            $right = $this->setColumnName($right);
        } else {
            $right = $this->filter('Right', $right, $right_filter);
        }

        $item            = new stdClass();
        $item->left      = $left;
        $item->condition = $condition;
        $item->right     = $right;
        $item->connector = $connector;
        $item->group     = $group;
        $this->having[]  = $item;

        return $this;
    }

    /**
     * Order By column name and optional value for alias
     *
     * @param   string      $column_name
     * @param   null|string $direction
     *
     * @return  $this
     * @since   1.0
     */
    public function orderBy($column_name, $direction = 'ASC')
    {
        if (trim($column_name) == '') {
            throw new RuntimeException ('Query-Order By Method: Value required for $column_name.');
        }

        $column = $this->setColumnName($column_name);

        $direction = strtoupper(trim($direction));
        if ($direction == 'DESC') {
            $column = $column . ' ' . 'DESC';
        } else {
            $column = $column . ' ' . 'ASC';
        }

        $this->order_by[] = $column;

        return $this;
    }

    /**
     * Offset and Limit
     *
     * @param   int $offset
     * @param   int $limit
     *
     * @return  $this
     * @since   1.0
     */
    public function setOffsetAndLimit($offset = 0, $limit = 0)
    {
        if ((int)$offset > 0) {
        } else {
            $offset = 0;
        }

        if ((int)$limit > 0) {
        } elseif ((int)$offset > 0) {
            $limit = 15;
        }

        $this->limit = $limit;

        $this->offset = $offset;

        return $this;
    }

    /**
     * Handle Column Name
     *
     * @param   string $column_name
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setColumnName($column_name)
    {
        if (strpos($column_name, '.')) {

            $temp = explode('.', $column_name);

            if (count($temp) == 2) {
                $prefix = $this->quoteName($temp[0]);

                if (trim($temp[1]) == '*') {
                    $column = $prefix . '.*';
                } else {
                    $column = $prefix . '.' . $this->quoteName($temp[1]);
                }

            } else {
                throw new RuntimeException
                ('Query-setColumnName Method: Illegal Value for $column_name: ' . $column_name);
            }

        } else {
            if (trim($column_name) == '*') {
                $column = '*';
            } else {
                $column = $this->quoteName($column_name);
            }
        }

        return $column;
    }

    /**
     * Process Array of Values for IN condition
     *
     * @param       string $filter
     * @param string $name
     * @param string $value_string
     */
    protected function processInArray($name, $value_string, $filter)
    {
        if (is_array($value_string) && count($value_string) > 0) {
            $temp         = implode(',', $value_string);
            $value_string = $temp;
        }

        $filtered_array = array();

        $temp = explode(',', $value_string);

        foreach ($temp as $value) {
            $filtered_array[] = $this->filter($name, trim($value), $filter);
        }

        return $filtered_array;
    }

    /**
     * Get SQL (optionally setting the SQL)
     *
     * @param   null|string $sql
     *
     * @return  string
     * @since   1.0
     */
    public function getSQL($sql = null)
    {
        if ($sql === null || trim($sql) == '') {
        } else {
            $this->sql = $this->setDatabasePrefix($sql);
            return $this->sql;
        }

        $this->sql = '';

        $this->query_type = strtolower($this->query_type);

        if ($this->query_type == 'insert') {
            $query = $this->setSQLInsert();

        } elseif ($this->query_type == 'insert-from') {
            $query = $this->setSQLInsertFrom();

        } elseif ($this->query_type == 'update') {
            $query = $this->setSQLUpdate();

        } elseif ($this->query_type == 'delete') {
            $query = $this->setSQLDelete();

        } else {
            $this->query_type = 'select';
            $query            = $this->setSQLSelect();
        }

        $this->sql = $this->setDatabasePrefix($query);

        return $this->sql;
    }

    /**
     * Generate SQL for Insert
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLInsert()
    {
        $query = 'INSERT INTO ' . $this->setFromSQL() . PHP_EOL;

        $string = '';

        foreach ($this->columns as $item) {

            if ($string == '') {
                $string = '(';
            } else {
                $string .= ', ';
            }

            $string .= trim($item->column);
        }

        $query .= $string . ')' . PHP_EOL;

        $string = '';

        foreach ($this->columns as $item) {

            if ($string == '') {
                $string = 'VALUES (';
            } else {
                $string .= ', ';
            }

            $string .= $item->value;
        }

        $query .= $string . ')' . PHP_EOL;

        return $query;
    }

    /**
     * Generate SQL for Insert using a select
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLInsertFrom()
    {
        return '';
    }

    /**
     * Generate SQL for Select
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setSQLSelect()
    {
        if (is_array($this->columns) && count($this->columns) > 0) {
        } else {
            throw new RuntimeException
            ('Query-setColumnSQL Method No SET $column_names provided.');
        }

        if ($this->distinct === true) {
            $query = 'SELECT DISTINCT ';
        } else {
            $query = 'SELECT ';
        }

        $string = '';

        foreach ($this->columns as $item) {

            if ($string == '') {
            } else {
                $string .= ', ' . PHP_EOL;
            }

            $string .= trim($item->column);

            if ($item->alias === null || trim($item->alias) == '') {
            } else {
                $string .= ' AS ' . $item->alias;
            }
        }

        $query .= $string . PHP_EOL;

        $query .= 'FROM ' . $this->setFromSQL() . PHP_EOL;

        if (count($this->where) > 0) {
            $query .= 'WHERE ' . $this->setWhereSQL() . PHP_EOL;
        }

        if (is_array($this->group_by) && count($this->group_by) > 0) {
            $query .= 'GROUP BY ' . $this->setGroupBySQL() . PHP_EOL;
        }

        if (count($this->having) > 0) {
            $query .= 'HAVING ' . $this->setHavingSQL() . PHP_EOL;
        }

        if (is_array($this->order_by) && count($this->order_by) > 0) {
            $query .= 'ORDER BY ' . $this->setOrderBySQL() . PHP_EOL;
        }

        if ((int)$this->offset == 0 && (int)$this->limit == 0) {
        } else {
            $query .= 'LIMIT ' . $this->setSQLLimit();
        }

        return $query;
    }

    /**
     * Generate SQL for Update
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLUpdate()
    {
        if (is_array($this->columns) && count($this->columns) > 0) {
        } else {
            throw new RuntimeException
            ('Query AbstractAdapter-setSQLUpdate Method: No columns to update.');
        }

        $query = 'UPDATE ' . $this->setFromSQL() . PHP_EOL;

        $query .= 'SET ';

        $string = '';

        foreach ($this->columns as $key => $item) {

            if ($item->data_type === null) {
                throw new RuntimeException
                ('Query-setSQLUpdate Method: No Datatype provided for Column Filter '
                . ' Column: ' . $item->column
                . ' Update to: ' . $query);
            }

            if ($string == '') {
            } else {
                $string .= ', ' . PHP_EOL;
            }

            $string .= trim($item->column) . ' = ' . $item->value;
        }

        $query .= $string . PHP_EOL;

        if (count($this->where) > 0) {
            $query .= 'WHERE ' . $this->setWhereSQL() . PHP_EOL;
        }

        return $query;
    }

    /**
     * Generate SQL for Delete
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLDelete()
    {
        $query = 'DELETE FROM ' . $this->setFromSQL() . PHP_EOL;

        if (count($this->where) > 0) {
            $query .= 'WHERE ' . $this->setWhereSQL() . PHP_EOL;
        }

        return $query;
    }

    /**
     * Generate SQL for Columns
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setFromSQL()
    {
        $string = '';

        if (is_array($this->from) && count($this->from) > 0) {

        } else {
            throw new RuntimeException
            ('Query-setFromSQL Method: Value required for table name.');
        }

        foreach ($this->from as $from) {

            if ($string == '') {
            } else {
                $string .= ', ' . PHP_EOL;
            }

            $string .= trim($from);
        }

        return $string;
    }

    /**
     * Generate SQL for Where Conditions
     *
     * @return  string
     * @since   1.0
     */
    protected function setWhereSQL()
    {
        if (is_array($this->where) && count($this->where) > 0) {
        } else {
            return '';
        }

        if (count($this->where_group) > 0) {
        } else {
            $this->where_group     = array();
            $this->where_group[''] = 'AND';
        }

        $group_string = '';

        foreach ($this->where_group as $where_group => $where_group_connector) {

            $string = '';

            foreach ($this->where as $where) {

                if (trim($where->group) == trim($where_group)) {

                    if (trim($string) == '') {
                    } else {
                        $string .= PHP_EOL . $where->connector . ' ';
                    }

                    $string .= $where->left . ' ' . strtoupper($where->condition);

                    if (strtolower($where->condition) == 'in') {
                        $in_string = '';

                        foreach ($where->right as $value) {

                            if ($in_string == '') {
                            } else {
                                $in_string .= ', ';
                            }

                            $in_string .= $value;
                        }

                        $where->right = '(' . trim($in_string) . ')';
                    }

                    $string .= ' ' . $where->right;
                }
            }

            if ($group_string == '') {
                if (trim($where_group) == '') {
                    $group_string .= $string . PHP_EOL;
                } else {
                    $group_string = '(';
                    $group_string .= $string . ')' . PHP_EOL;
                }

            } else {
                $group_string .= ' ' . $where_group_connector . ' (';
                $group_string .= $string . ')' . PHP_EOL;
            }
        }

        return $group_string;
    }

    /**
     * Generate SQL for Group By
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setGroupBySQL()
    {
        $string = '';

        if (is_array($this->group_by) && count($this->group_by) > 0) {
        } else {
            return $string;
        }

        foreach ($this->group_by as $group_by) {

            if ($string == '') {
            } else {
                $string .= ', ' . PHP_EOL;
            }

            $string .= trim($group_by);
        }

        return $string;
    }

    /**
     * Generate SQL for Having Conditions
     *
     * @return  string
     * @since   1.0
     */
    protected function setHavingSQL()
    {
        if (is_array($this->having) && count($this->having) > 0) {
        } else {
            return '';
        }

        if (count($this->having_group) > 0) {
        } else {
            $this->having_group     = array();
            $this->having_group[''] = 'AND';
        }

        $group_string = '';

        foreach ($this->having_group as $having_group => $having_group_connector) {

            $string = '';

            foreach ($this->having as $having) {

                if (trim($having->group) == trim($having_group)) {

                    if (trim($string) == '') {
                    } else {
                        $string .= PHP_EOL . $having->connector . ' ';
                    }

                    $string .= $having->left . ' ' . $having->condition . ' ' . $having->right;
                }
            }

            if ($group_string == '') {
                if (trim($having_group) == '') {
                    $group_string .= $string . PHP_EOL;
                } else {
                    $group_string = '(';
                    $group_string .= $string . ')' . PHP_EOL;
                }

            } else {
                $group_string .= ' ' . $having_group_connector . ' (';
                $group_string .= $string . ')' . PHP_EOL;
            }
        }

        return $group_string;
    }

    /**
     * Generate SQL for Order By
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setOrderBySQL()
    {
        $string = '';

        if (is_array($this->order_by) && count($this->order_by) > 0) {
        } else {
            return $string;
        }

        foreach ($this->order_by as $order_by) {

            if ($string == '') {
            } else {
                $string .= ', ' . PHP_EOL;
            }

            $string .= trim($order_by);
        }

        return $string;
    }

    /**
     * Generate SQL for Select
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLLimit()
    {
        return $this->offset . ', ' . $this->limit . PHP_EOL;
    }

    /**
     * Filter Input
     *
     * @param   string $key
     * @param   null|string  $value
     * @param   string $data_type
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function filter($key, $value = null, $data_type)
    {
        try {
            $results = $this->fieldhandler->sanitize($key, $value, $data_type);

            $value = $results->getFieldValue();

        } catch (Exception $e) {
            throw new RuntimeException
            ('Request: Filter class Failed for Key: ' . $key . ' Filter: ' . $data_type . ' ' . $e->getMessage());
        }

        return $this->quoteValue($this->database->escape($value));
    }

    /**
     * Set Database Prefix
     *
     * @param   string $query
     *
     * @return  string
     * @since   1.0
     */
    protected function setDatabasePrefix($query)
    {
        return str_replace('#__', $this->database_prefix, $query);
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
     *
     * @return  string
     * @since   1.0
     */
    protected function quoteName($value)
    {
        return $this->name_quote_start . $value . $this->name_quote_end;
    }
}
