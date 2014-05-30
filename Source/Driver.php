<?php
/**
 * Query Driver
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use CommonApi\Query\QueryInterface;

/**
 * Adapter for Query
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Driver implements QueryInterface
{
    /**
     * Query Adapter
     *
     * @var     object  CommonApi\Query\QueryInterface
     * @since   1.0
     */
    protected $adapter;

    /**
     * Constructor
     *
     * @param  QueryInterface $adapter
     *
     * @since  1.0
     */
    public function __construct(QueryInterface $adapter)
    {
        $this->adapter = $adapter;
    }

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
        return $this->adapter->get($key, $default);
    }

    /**
     * Clear Query String
     *
     * @return  $this
     * @since   1.0
     */
    public function clearQuery()
    {
        return $this->adapter->clearQuery();
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
        return $this->adapter->setType($query_type);
    }

    /**
     * Returns a database driver compliant date format for PHP date()
     *
     * @return  string
     * @since   1.0
     */
    public function getDateFormat()
    {
        return $this->adapter->getDateFormat();
    }

    /**
     * Retrieves the current date and time formatted compliant with the database driver
     *
     * @return  string
     * @since   1.0
     */
    public function getDate()
    {
        return $this->adapter->getDate();
    }

    /**
     * Returns a database driver compliant value for null date
     *
     * @return  string
     * @since   1.0
     */
    public function getNullDate()
    {
        return $this->adapter->getNullDate();
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
        return $this->adapter->setDistinct($distinct);
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
        return $this->adapter->select($column_name, $alias, $value, $data_type);
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
        return $this->adapter->from($table_name, $alias);
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
        return $this->adapter->whereGroup($group, $group_connector);
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
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = null
    ) {
        return $this->adapter->where($left_filter, $left, $condition, $right_filter, $right, $connector, $group);
    }

    /**
     * Group By column name
     *
     * @param   string      $column_name
     *
     * @return  $this
     * @since   1.0
     */
    public function groupBy($column_name)
    {
        return $this->adapter->groupBy($column_name);
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
        return $this->adapter->havingGroup($group, $group_connector);
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
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = null
    ) {
        return $this->adapter->having($left_filter, $left, $condition, $right_filter, $right, $connector, $group);
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
        return $this->adapter->orderBy($column_name, $direction);
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
        return $this->adapter->setOffsetAndLimit($offset, $limit);
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
        return $this->adapter->getSQL($sql);
    }
}
