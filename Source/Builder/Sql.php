<?php
/**
 * Query Builder Sql Class
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

use CommonApi\Database\DatabaseInterface;
use CommonApi\Model\FieldhandlerInterface;
use DateTime;

/**
 * Query Builder Sql Class
 *
 * Adapters extend this class
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Sql extends Generate
{
    /**
     * Constructor
     *
     * @since  1.0.0
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
     * @since   1.0.0
     */
    public function get($key, $default = null)
    {
        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Clear Query String
     *
     * @return  $this
     * @since   1.0.0
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
        if (in_array(strtolower($query_type), $this->query_type_array)) {
            $this->query_type = strtolower($query_type);
        } else {
            $this->query_type = 'select';
        }

        return $this;
    }

    /**
     * Retrieves the current date and time formatted in a manner compliant with the database driver
     *
     * @return  string
     * @since   1.0.0
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
     * @since   1.0.0
     */
    public function getNullDate()
    {
        return $this->null_date;
    }

    /**
     * Retrieves the PHP date format compliant with the database driver
     *
     * @return  string
     * @since   1.0.0
     */
    public function getDateFormat()
    {
        return $this->date_format;
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
     */
    public function select($column_name, $alias = null, $value = null, $data_type = 'string')
    {
        $this->editRequired('column_name', $column_name);
        $this->columns[$column_name] = $this->setItem($column_name, 'string', $column_name, $alias);

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
     */
    public function from($table_name, $alias = null)
    {
        $this->editRequired('table_name', $table_name);
        $this->from[$table_name] = $this->setItem($table_name, 'string', $table_name, $alias);

        return $this;
    }

    /**
     * Create a grouping for 'and' or 'or' groups of where conditions
     *
     * @param   string $group
     * @param   string $group_connector
     *
     * @return  $this
     * @since   1.0
     */
    public function whereGroup($group, $group_connector = 'AND')
    {
        $this->where_group = $this->setGroup($group, $group_connector, 'where', $this->where_group);

        return $this;
    }

    /**
     * Create a grouping for 'and' or 'or' groups of having conditions
     *
     * @param   string $group
     * @param   string $group_connector
     *
     * @return  $this
     * @since   1.0
     */
    public function havingGroup($group, $group_connector = 'AND')
    {
        $this->having_group = $this->setGroup($group, $group_connector, 'having', $this->having_group);

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
     * @param   string|null $group
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
        $group = ''
    ) {
        $item = $this->setLeftRightConditionals(
            $left_filter,
            $left,
            $condition,
            $right_filter,
            $right,
            $connector,
            $group
        );

        $this->where[] = $item;

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
     */
    public function having(
        $left_filter = 'column',
        $left = '',
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = ''
    ) {
        $item = $this->setLeftRightConditionals(
            $left_filter,
            $left,
            $condition,
            $right_filter,
            $right,
            $connector,
            $group
        );

        $this->having[] = $item;

        return $this;
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
        $this->group_by[] = $this->setGroupByOrderBy($column_name, 'group by');

        return $this;
    }

    /**
     * Order By column name
     *
     * @param   string      $column_name
     * @param   null|string $direction
     *
     * @return  $this
     * @since   1.0
     */
    public function orderBy($column_name, $direction = 'ASC')
    {
        $this->order_by[] = $this->setGroupByOrderBy($column_name, 'order by', $direction);

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
        $this->setOffsetorLimit($offset, $type = 'offset');
        $this->setOffsetorLimit($limit, $type = 'limit');

        return $this;
    }

    /**
     * Get SQL - all values have been filtered and set, generate the full SQL statement,
     *  escaping data
     *
     * @param   null|string $sql
     *
     * @return  string
     * @since   1.0
     */
    public function getSql($sql = null)
    {
        if ($sql === null || trim($sql) === '') {
            $this->generateSql();
        } else {
            $this->sql = '';
            $this->getExternalSql($sql);
        }

        return $this->sql;
    }
}
