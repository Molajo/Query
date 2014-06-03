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

        $a = $this->groups_array[$type];

        if ($type === 'where' || $type === 'having') {
            $output = $this->getGroups($this->{$type . '_group'}, $this->$type, $a['connector']);
        } else {
            $array  = $this->getElementsArray($type, $a['get_value'], $a['get_column'], $a['use_alias']);
            $output = $this->getLoop($array, $a['key_value'], $a['format']);
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
        $prefix = '';

        if (count($this->from) === 1) {
            return $prefix;
        }

        $prefix = $this->getPrimaryColumnPrefix();

        $column_array = $this->columns;
        $this->columns = array();

        foreach ($column_array as $key => $column) {

            if ($column->prefix === '') {
                $column->prefix = $prefix;
            }

            $this->columns[$key] = $column;
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
        $prefix = '';

        if (count($this->from) === 1) {
            return $prefix;
        }

        foreach ($this->from as $key => $from) {

            if ($from->primary === true) {
                if ($from->alias === null || trim($from->alias === '')) {
                    $prefix = $from->name;
                    break;
                }
                $prefix = $from->alias;
                break;
            }
        }

        return $prefix;
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

    /**
     * Set Offset or Limit
     *
     * @param   integer $value
     * @param   string  $type
     *
     * @return  $this
     * @since   1.0
     */
    protected function setOffsetOrLimit($value, $type = 'offset')
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

    /**
     * Set From table name and optional value for alias
     *
     * @param   string $table_name
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setPrimaryTable($table_name)
    {
        $from_array = $this->from;
        $this->from = array();

        foreach ($from_array as $key => $from) {

            if ($key === $table_name) {
                $from->primary = true;
            } else {
                $from->primary = false;
            }

            $this->from[$key] = $from;
        }

        return false;
    }

    /**
     * Set From table name and optional value for alias
     *
     * @return  boolean
     * @since   1.0
     */
    protected function existsPrimaryTable()
    {
        $primary_key = false;

        $from_array = $this->from;
        $this->from = array();

        foreach ($from_array as $key => $from) {

            if (isset($from->primary)) {
            } else {
                $from->primary = false;
            }

            if ($from->primary === true) {
                $primary_key = true;
            }

            $this->from[$key] = $from;
        }

        return $primary_key;
    }
}
