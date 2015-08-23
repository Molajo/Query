<?php
/**
 * Query Builder Build Sql Elements
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

/**
 * Query Builder Build Sql Elements
 *
 * Sql - BuildSql - BuildSqlElements - BuildSqlGroups - SetData - EditData - FilterData - Base
 *
 * Processes output data, already filtered will be escaped in getLoop
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class BuildSqlElements extends BuildSqlGroups
{
    /**
     * Generate Element SQL
     *
     * @param   string $type
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getElement($type)
    {
        if ($this->useGetElement($type) === false) {
            return '';
        }

        if ($type === 'limit') {
            return $this->getElementLimit();

        } elseif ($type === 'insert_into_table') {
            return $this->getElementInsertInto();
        }

        return $this->getElementStandard($type);
    }

    /**
     * Get Standard Element
     *
     * @param   string $type
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getElementStandard($type)
    {
        $a = $this->groups_array[$type];

        if ($type === 'where' || $type === 'having') {
            $output = $this->getGroups($this->{$type . '_group'}, $this->$type, $a['connector']);
        } else {
            $array  = $this->getElementsArray($type, $a['get_value'], $a['get_column'], $a['use_alias']);
            $output = $this->getLoop($array, $a['key_value']);
        }

        return $this->returnGetElement($a['return_literal'], $output);
    }

    /**
     * Does SQL for this element need to be built?
     *
     * @param   string $type
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function useGetElement($type)
    {
        if ($type === 'values') {
            $type = 'columns';
        }

        if (count($this->$type) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Generate Table Name for Insert
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getElementInsertInto()
    {
        return (string)$this->insert_into_table;
    }

    /**
     * Generate SQL for Limit
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getElementLimit()
    {
        if ((int)$this->offset === 0 && (int)$this->limit === 0) {
        } else {
            return 'LIMIT ' . $this->offset . ', ' . $this->limit;
        }

        return '';
    }

    /**
     * Return getElement Value
     *
     * @param   string $return_literal
     * @param   string $output
     *
     * @return  string
     * @since   1.0.0
     */
    protected function returnGetElement($return_literal, $output = '')
    {
        if (trim($output) === '') {
            return '';
        }

        return trim($return_literal . ' ' . $output);
    }

    /**
     * Generate array of column names, values, or name-value pairs
     *
     * @param   string  $type
     * @param   boolean $get_value
     * @param   boolean $get_column
     * @param   boolean $use_alias
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getElementsArray($type, $get_value = true, $get_column = true, $use_alias = true)
    {
        $array = array();

        if ($type === 'columns') {
            $this->setColumnPrefix();
        }

        foreach ($this->$type as $item) {
            $array = $this->getElementsArrayItem($item, $get_value, $get_column, $use_alias, $array);
        }

        return $array;
    }

    /**
     * Generate array of column names, values, or name-value pairs
     *
     * @param   object  $item
     * @param   boolean $get_value
     * @param   boolean $get_column
     * @param   boolean $use_alias
     * @param   array   $array
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getElementsArrayItem($item, $get_value, $get_column, $use_alias, $array)
    {
        if ($item->data_type === 'special') {
            return $this->getElementsArrayItemSpecial($item, $array);
        }

        $column_name = $this->getElementValuesColumnName($item, $get_column, $use_alias);
        $value       = $this->getElementValuesValue($item, $get_value);

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
     * Special item->name
     *
     * @param   object $item
     * @param   array  $array
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getElementsArrayItemSpecial($item, $array)
    {
        $array[] = $item->name;

        return $array;
    }

    /**
     * Assign a default column prefix, if needed
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setColumnPrefix()
    {
        if (count($this->from) === 1) {
            return $this;
        }

        $prefix = $this->getPrimaryColumnPrefix();

        foreach ($this->columns as $key => $column) {
            if ($this->columns[$key]->prefix === '') {
                $this->columns[$key]->prefix = $prefix;
            }
        }

        return $this;
    }

    /**
     * Get the Primary Prefix for use with columns that have no prefix
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getPrimaryColumnPrefix()
    {
        $key = $this->findFromPrimary();

        if ($key == false) {
            return '';
        }

        if ($this->from[$key]->alias === '') {
            return $this->from[$key]->name;
        }

        return $this->from[$key]->alias;
    }

    /**
     * Get Column Name
     *
     * @param   object  $item
     * @param   boolean $get_column
     * @param boolean   $use_alias
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getElementValuesColumnName($item, $get_column, $use_alias)
    {
        $column_name = '';

        if ($get_column === true) {
            $column_name = $this->quoteNameAndPrefix($item->name, $item->prefix);
        }

        if ($use_alias === true && isset($item->alias)) {
            $column_name .= $this->setColumnAlias($use_alias, $item->alias);
        }

        return $column_name;
    }

    /**
     * Get Column Value
     *
     * @param   object  $item
     * @param   boolean $get_value
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getElementValuesValue($item, $get_value)
    {
        if ($get_value === true) {
            return $this->quoteValue($item->value);
        }

        return '';
    }

    /**
     * Set Column Alias
     *
     * @param   boolean $use_alias
     * @param   string  $alias
     *
     * @return  string
     * @since   1.0.0
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
}
