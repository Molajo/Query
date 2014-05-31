<?php
/**
 * Abstract Query Builder Collect
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

use CommonApi\Query\QueryInterface;
use stdClass;

/**
 * Abstract Collect Query Elements
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Collect extends Edits implements QueryInterface
{
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
        if ($sql === null) {
        } else {
            $this->sql = $sql;
        }

        return $this->sql;
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
    public function select($column_name, $alias = null, $value = null, $data_type = null)
    {
        $this->editRequired('column_name', $column_name);
        $this->columns[$column_name] = $this->setItem($column_name, $data_type, $value, $alias);

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
     * Groups for 'and' or 'or' groups for both where and having
     *
     * @param   string $group
     * @param   string $group_connector
     * @param   string $type
     * @param   array  $group_array
     *
     * @return  array
     * @since   1.0
     */
    protected function setGroup($group, $group_connector = 'AND', $type = 'where', array $group_array = array())
    {
        $this->editRequired('group', $group);

        $group               = $this->filter($type, $group, 'string');
        $group_connector     = $this->editConnector($group_connector);
        $group_array[$group] = $group_connector;

        return $group_array;
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
            $group,
            'where'
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
            $group,
            'having'
        );

        $this->having[] = $item;

        return $this;
    }

    /**
     * Set Conditions for Query - used for Where and Having
     *
     * @param   string      $left_filter
     * @param   string      $left
     * @param   string      $condition
     * @param   string      $right_filter
     * @param   string      $right
     * @param   string      $connector
     * @param   null|string $group
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function setLeftRightConditionals(
        $left_filter = 'column',
        $left = '',
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = ''
    ) {
        $this->editWhere($left, $condition, $right);

        $item             = new stdClass();
        $item->group      = (string) trim($group);
        $item->left_item  = $this->setItem($left, $left_filter, $left);
        $item->condition  = $condition;
        $item->right_item = $this->setItem($right, $right_filter, $right, null, $condition);
        $item->connector  = $this->editConnector($connector);

        return $item;
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
     * Order By column name and optional value for alias
     *
     * @param   string      $column_name
     * @param   string      $type
     * @param   null|string $direction
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function setGroupByOrderBy($column_name, $type, $direction = 'ASC')
    {
        $this->editRequired('group by column_name', $column_name);

        $name_and_prefix = $this->setItemName($column_name);

        $item         = new stdClass();
        $item->name   = (string)$name_and_prefix['name'];
        $item->prefix = (string)$name_and_prefix['prefix'];

        if ($type === 'order by') {
            $item->direction = $this->setDirection($direction);
        }

        return $item;
    }

    /**
     * Set Direction
     *
     * @param   string $direction
     *
     * @return  $this
     * @since   1.0
     */
    protected function setDirection($direction = 'DESC')
    {
        $direction = strtoupper($direction);

        if ($direction === 'ASC') {
            return 'ASC';
        }

        return 'DESC';
    }

    /**
     * Set Item Object with Data
     *
     * @param   string      $name
     * @param   null|string $data_type
     * @param   null|string $value
     * @param   null|string $alias
     * @param   null|string $condition
     *
     * @return  $this
     * @since   1.0
     */
    protected function setItem($name, $data_type, $value = null, $alias = null, $condition = null)
    {
        $this->editRequired('name', $name);

        $name_and_prefix = $this->setItemName($name);

        $item            = new stdClass();
        $item->name      = (string)$name_and_prefix['name'];
        $item->prefix    = (string)$name_and_prefix['prefix'];
        $item->data_type = (string)$this->setItemDataType($data_type);

        if ($condition === 'in') {
            $item->value = $this->setItemValueInDataType($value, $data_type);
        } else {
            $item->value = $this->filter($item->name, $value, $data_type);
        }

        if ($alias === null || trim($alias) === '') {
            $item->alias = null;
        } else {
            $item->alias = (string)$alias;
        }

        return $item;
    }

    /**
     * Set Item Column Name and Prefix
     *
     * @param   string $column_name
     *
     * @return  string
     * @since   1.0
     */
    protected function setItemName($column_name)
    {
        if (strpos($column_name, '.')) {
            $temp   = explode('.', $column_name);
            $prefix = (string)$temp[0];
            $prefix .= '.';
            $column_name = (string)$temp[1];
        } else {
            $prefix = null;
        }

        return array('prefix' => $prefix, 'name' => $column_name);
    }

    /**
     * Set the Item data type
     *
     * @param   string  $data_type
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function setItemDataType($data_type)
    {
        if ($data_type === null || trim($data_type) === '') {
            $data_type = 'string';
        }

        return $data_type;
    }

    /**
     * Set the Item Value for "In" Condition
     *
     * @param   null|string $value
     * @param   string      $data_type
     *
     * @return  array
     * @since   1.0
     */
    protected function setItemValueInDataType($value, $data_type)
    {
        $in_array = explode(',', $value);
        $value    = array();

        foreach ($in_array as $value) {
            $value[] = $this->filter('In array value', $value, $data_type);
        }

        return $value;
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
     * Set Offset or Limit
     *
     * @param   integer $value
     * @param   string  $type
     *
     * @return  $this
     * @since   1.0
     */
    protected function setOffsetorLimit($value, $type = 'offset')
    {
        if ((int)$value > 0) {
        } else {
            $value = 0;
        }

        if ($type === 'limit') {
            if ((int)$value === 0) {
                $value = 15;
            }
        }

        $this->$type = $value;

        return $this;
    }
}
