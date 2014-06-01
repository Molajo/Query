<?php
/**
 * Query Builder Elements
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

use stdClass;

/**
 * Query Builder Elements
 *
 * Base - Filters - Edits - Elements - Groups - Generate - Sql
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Elements extends Edits
{
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

            $column_value_array = $this->getElementValues($item, $get_value, $get_column, $use_alias);
            $column_name        = $column_value_array['column_name'];
            $value              = $column_value_array['value'];

            $array = $this->getElementArrayEntry($array, $column_name, $value, $get_value, $get_column);
        }

        return $array;
    }

    /**
     * Get Column Name and Value
     *
     * @param   object  $item
     * @param   boolean $get_value
     * @param   boolean $get_column
     * @param   boolean $use_alias
     *
     * @return  string
     * @since   1.0
     */
    protected function getElementValues($item, $get_value = true, $get_column = true, $use_alias = true)
    {
        $column_name = '';
        $value       = '';

        if ($get_column === true) {
            $column_name = $this->setColumnName($item->name);
            $column_name .= $this->setColumnAlias($use_alias, $item->alias);
        }

        if ($get_value === true) {
            $value = $this->setColumnValue($item->name, $item->value, $item->data_type);
        }

        return array('column_name' => $column_name, 'value' => $value);
    }

    /**
     * Create an array entry for column name and value
     *
     * @param   array   $array
     * @param   string  $column_name
     * @param   string  $value
     * @param   boolean $get_value
     * @param   boolean $get_column
     *
     * @return  array
     * @since   1.0
     */
    protected function getElementArrayEntry(array $array, $column_name, $value, $get_value = true, $get_column = true)
    {
        if ($get_value === true && $get_column === true) {
            $array[$column_name] = $value;

        } elseif ($get_value === true) {
            $array[] = $value;

        } else {
            $array[] = $column_name;
        }

        return $array;
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
