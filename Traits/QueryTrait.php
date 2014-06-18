<?php
/**
 * Query Builder Trait
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

/**
 * Query Builder Trait
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
trait QueryTrait
{
    /**
     * Query Builder
     *
     * @var     object  CommonApi\Query\QueryInterface
     * @since   1.0
     */
    protected $query;

    /**
     * Query Object
     *
     * List, Item, Result, Distinct
     *
     * @var    string
     * @since  1.0
     */
    protected $query_object;

    /**
     * Use Pagination
     *
     * @var    integer
     * @since  1.0
     */
    protected $use_pagination = 0;

    /**
     * Offset
     *
     * @var    integer
     * @since  1.0
     */
    protected $offset;

    /**
     * Limit
     *
     * @var    int
     * @since  1.0
     */
    protected $limit = 0;

    /**
     * Offset Count
     *
     * @var    integer
     * @since  1.0
     */
    protected $offset_count;

    /**
     * Total
     *
     * @var    integer
     * @since  1.0
     */
    protected $total;

    /**
     * Count
     *
     * @var    integer
     * @since  1.0
     */
    protected $count;

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
     * Null Date
     *
     * @var    string
     * @since  1.0
     */
    protected $current_date = '';

    /**
     * SQL
     *
     * @var    string
     * @since  1.0
     */
    protected $sql = '';

    /**
     * Get the current value (or default) of the specified property
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->query->get($key, $default);
    }

    /**
     * Clear Query String
     *
     * @return  $this
     * @since   1.0
     */
    public function clearQuery()
    {
        return $this->query->clearQuery();
    }

    /**
     * Set Query Type - create, select (default), update, delete, exec
     *
     * @param   string $query_type
     *
     * @return  $this
     * @since   1.0
     */
    public function setType($query_type = 'select')
    {
        return $this->query->setType($query_type);
    }

    /**
     * Retrieves the current date and time formatted compliant with the database driver
     *
     * @return  string
     * @since   1.0
     */
    public function getDate()
    {
        return $this->query->getDate();
    }

    /**
     * Returns a database driver compliant value for null date
     *
     * @return  string
     * @since   1.0
     */
    public function getNullDate()
    {
        return $this->query->getNullDate();
    }

    /**
     * Returns a database driver compliant date format for PHP date()
     *
     * @return  string
     * @since   1.0
     */
    public function getDateFormat()
    {
        return $this->query->getDateFormat();
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
        return $this->query->setDistinct($distinct);
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
     */
    public function select($column_name, $alias = null, $value = null, $data_type = null)
    {
        return $this->query->select($column_name, $alias, $value, $data_type);
    }

    /**
     * Set From table name and optional value for alias
     *
     * @param   string      $table_name
     * @param   null|string $alias
     *
     * @return  $this
     * @since   1.0
     */
    public function from($table_name, $alias = null)
    {
        return $this->query->from($table_name, $alias);
    }

    /**
     * Create a grouping for conditions for 'and' or 'or' treatment between groups of conditions
     *
     * @param   string $group
     * @param   string $group_connector
     *
     * @return  $this
     * @since   1.0
     */
    public function whereGroup($group, $group_connector = 'and')
    {
        return $this->query->whereGroup($group, $group_connector);
    }

    /**
     * Create a grouping for having statements for 'and' or 'or' treatment between groups of conditions
     *
     * @param   string $group
     * @param   string $group_connector
     *
     * @return  $this
     * @since   1.0
     */
    public function havingGroup($group, $group_connector = 'and')
    {
        return $this->query->havingGroup($group, $group_connector);
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
     */
    public function where(
        $left_filter = 'column',
        $left = '',
        $condition = '',
        $right_filter = 'column',
        $right = '',
        $connector = 'and',
        $group = null
    ) {
        return $this->query->where($left_filter, $left, $condition, $right_filter, $right, $connector, $group);
    }

    /**
     * Set Having Conditions for Query
     *
     * @param   string $left_filter
     * @param   string $left
     * @param   string $condition
     * @param   string $right_filter
     * @param   string $right
     * @param   string $connector
     * @param   string $group
     *
     * @return  $this
     * @since   1.0
     */
    public function having(
        $left_filter = 'column',
        $left = '',
        $condition = '',
        $right_filter = 'column',
        $right = '',
        $connector = 'and',
        $group = null
    ) {
        return $this->query->having($left_filter, $left, $condition, $right_filter, $right, $connector, $group);
    }

    /**
     * Group By column name
     *
     * @param   string $column_name
     *
     * @return  $this
     * @since   1.0
     */
    public function groupBy($column_name)
    {
        return $this->query->groupBy($column_name);
    }

    /**
     * Set Order By column name and optional value for alias
     *
     * @param   string      $column_name
     * @param   null|string $direction
     *
     * @return  $this
     * @since   1.0
     */
    public function orderBy($column_name, $direction = 'ASC')
    {
        return $this->query->orderBy($column_name, $direction);
    }

    /**
     * Set Offset and Limit
     *
     * @param   int $offset
     * @param   int $limit
     *
     * @return  $this
     * @since   1.0
     */
    public function setOffsetAndLimit($offset = 0, $limit = 15)
    {
        return $this->query->setOffsetAndLimit($offset, $limit);
    }

    /**
     * Get SQL (optionally setting the SQL)
     *
     * @param   null|string $sql
     *
     * @return  string
     * @since   1.0
     */
    public function getSql($sql = null)
    {
        return $this->query->getSql($sql);
    }
}
