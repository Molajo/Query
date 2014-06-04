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
     * @since   1.0
     */
    protected function getElement($type)
    {
        if ($this->useGetElement($type) === false) {
            return '';
        }

        if ($type === 'limit') {
            return $this->getElementLimit();
        }

        return $this->getElementStandard($type);
    }

    /**
     * Get Standard Element
     *
     * @param   $type
     *
     * @return  string
     * @since   1.0
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
     * @since   1.0
     */
    protected function useGetElement($type)
    {
        if (count($this->$type) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Generate SQL for Limit
     *
     * @return  string
     * @since   1.0
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
     * @since   1.0
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
     * @since   1.0
     */
    protected function getElementsArray($type, $get_value = true, $get_column = true, $use_alias = true)
    {
        $array = array();

        if ($type === 'columns') {
            $this->setColumnPrefix();
        }

        foreach ($this->$type as $item) {
            $column_name = $this->getElementValuesColumnName($item, $get_column);
            $column_name .= $this->getElementValuesAlias($item, $use_alias);
            $value = $this->getElementValuesValue($item, $get_value);
            $array = $this->getElementArrayEntry($array, $column_name, $value, $get_value, $get_column);
        }

        return $array;
    }

    /**
     * Assign a default column prefix, if needed
     *
     * @return  $this
     * @since   1.0
     */
    protected function setColumnPrefix()
    {
        if (count($this->from) === 1) {
            return '';
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
     * @since   1.0
     */
    protected function getPrimaryColumnPrefix()
    {
        $key = $this->findFromPrimary();

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
     *
     * @return  string
     * @since   1.0
     */
    protected function getElementValuesColumnName($item, $get_column)
    {
        if ($get_column === true) {
            return $this->quoteNameAndPrefix($item->name, $item->prefix);
        }

        return '';
    }

    /**
     * Get Column Name Alias
     *
     * @param   object  $item
     * @param   boolean $use_alias
     *
     * @return  string
     * @since   1.0
     */
    protected function getElementValuesAlias($item, $use_alias)
    {
        if ($use_alias === true && isset($item->alias)) {
            return $this->setColumnAlias($use_alias, $item->alias);
        }

        return '';
    }

    /**
     * Get Column Value
     *
     * @param   object  $item
     * @param   boolean $get_value
     *
     * @return  string
     * @since   1.0
     */
    protected function getElementValuesValue($item, $get_value)
    {
        if ($get_value === true) {
            return $this->quoteValue($item->value);
        }

        return '';
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
}
