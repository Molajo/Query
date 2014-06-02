<?php
/**
 * Query Builder Item
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

use stdClass;

/**
 * Query Builder Item
 *
 * Base - Filters - Edits - Item - Elements - Groups - Generate - Sql
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Item extends Edits
{

    /**
     * Set Item Object with Data
     *
     * @param   string      $name
     * @param   null|string $data_type
     * @param   null|string $value
     * @param   null|string $alias
     * @param   null|string $condition
     * @param   null|string $type
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function setItem($name, $data_type, $value = null, $alias = null, $condition = null, $filter = false)
    {
        $this->editRequired('name', $name);

        $name_and_prefix = $this->setItemName($name);

        $item            = new stdClass();
        $item->name      = (string)$name_and_prefix['name'];
        $item->prefix    = (string)$name_and_prefix['prefix'];
        $item->data_type = (string)$this->setItemDataType($data_type);
        $item->value     = $this->setItemValue($item->name, $data_type, $value, $condition, $filter);
        $item->alias     = $this->setItemAlias($alias);

        return $item;
    }

    /**
     * Set Item Value
     *
     * @param   string      $name
     * @param   null|string $data_type
     * @param   null|string $value
     * @param   null|string $condition
     * @param   boolean     $filter
     *
     * @return  mixed
     * @since   1.0
     */
    protected function setItemValue($name, $data_type, $value = null, $condition = null, $filter = false)
    {
        if ($filter === false) {
            return $value;
        }

        if ($condition === 'in') {
            return $this->setItemValueInDataType($value, $data_type);
        }

        return $this->filter($name, $value, $data_type);
    }

    /**
     * Set Item Alias
     *
     * @param   null|string $alias
     *
     * @return  string
     * @since   1.0
     */
    protected function setItemAlias($alias = null)
    {
        if ($alias === null || trim($alias) === '') {
            return null;
        }

        return (string)$alias;
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
     * @param   string $data_type
     *
     * @return  string
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
}
