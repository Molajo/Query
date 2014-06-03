<?php
/**
 * Query Builder Build Sql Elements
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

/**
 * Query Builder Build Sql Elements
 *
 * Sql - BuildSql - BuildSqlGroups - BuildSqlElements - SetData - EditData - FilterData - Base
 *
 * Processes output data, already filtered will be escaped in getLoop
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class BuildSqlElements extends SetData
{
    /**
     * Generate array of column names, values, or name-value pairs
     *
     * @param   array   $type_array
     * @param   boolean $get_value
     * @param   boolean $get_column
     * @param   boolean $use_alias
     *
     * @return  string
     * @since   1.0
     */
    protected function getElementsArray(array $type_array, $get_value = true, $get_column = true, $use_alias = true)
    {
        $array = array();

        foreach ($type_array as $item) {

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

            if ($use_alias === true && isset($item->alias)) {
                $column_name .= $this->setColumnAlias($use_alias, $item->alias);
            }
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
     * @return  string
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
     * Set Direction
     *
     * @param   string $direction
     *
     * @return  string
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
