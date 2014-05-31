<?php
/**
 * Abstract Query Builder - Edits
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

use CommonApi\Exception\RuntimeException;
use Exception;

/**
 * Query Builder Edits
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Edits extends Base
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
     * Edit Array
     *
     * @param   mixed   $array
     * @param   string  $type
     * @param   boolean $exception
     *
     * @return  array
     * @since   1.0
     */
    protected function editArray($array, $type = 'columns', $exception = true)
    {
        if (is_array($array) && count($array) > 0) {
            return $array;
        }

        if ($exception === true) {
            throw new RuntimeException('editArray Method: ' . $type . ' does not have data.');
        }

        return array();
    }

    /**
     * Tests if a required value has been provided
     *
     * @param   string $data_type
     * @param   string $column_name
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function editDataType($data_type = null, $column_name = '')
    {
        if ($data_type === null) {
            throw new RuntimeException(
                'Query-editDataType Method: No Datatype provided for Column: ' . $column_name
            );
        }
    }

    /**
     * Tests if a required value has been provided
     *
     * @param   string $column_name
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function editRequired($column_name, $value = null)
    {
        if (trim($value) === '' || $value === null) {
            throw new RuntimeException('Query: Value required for: ' . $column_name);
        }
    }

    /**
     * Edit Connector
     *
     * @param   string $connector
     *
     * @return  string
     * @since   1.0
     */
    protected function editConnector($connector = null)
    {
        $connector = strtoupper($connector);

        if (in_array($connector, $this->connector)) {
        } else {
            $connector = 'AND';
        }

        return $connector;
    }

    /**
     * Edit WHERE
     *
     * @param   string $left
     * @param   string $right
     * @param   string $condition
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function editWhere($left, $condition, $right)
    {
        if (trim($left) === ''
            || trim($condition) === ''
            || trim($right) === ''
        ) {
            throw new RuntimeException(
                'Query-Where Method: Value required for ' . ' $left: ' . $left
                . ' $condition: ' . $condition . ' $right: ' . $right
            );
        }

        return $this;
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
     * @param   string $alias
     *
     * @return  string
     * @since   1.0
     */
    protected function quoteNameAndPrefix($value, $prefix = null)
    {
        if ($prefix === null || trim($prefix) === '') {
            $return_prefix = '';

        } else {
            $prefix        = $this->quoteName($prefix);
            $return_prefix = $prefix . '.';
        }

        $value = $this->quoteName($value);

        return $return_prefix . $value;
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
        if (trim($value) === '*') {
            return $value;
        }

        return $this->name_quote_start . $value . $this->name_quote_end;
    }

    /**
     * Filter Input
     *
     * @param   string      $key
     * @param   null|string $value
     * @param   string      $data_type
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function filter($key, $value = null, $data_type = 'string')
    {
        try {
            $results = $this->fieldhandler->sanitize($key, $value, ucfirst(strtolower($data_type)));

            $value = $results->getFieldValue();

        } catch (Exception $e) {
            throw new RuntimeException(
                'Request: Filter class Failed for Key: ' . $key . ' Filter: ' . $data_type . ' ' . $e->getMessage()
            );
        }

        return $this->quoteValue($value);
    }
}
