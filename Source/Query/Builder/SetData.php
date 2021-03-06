<?php
/**
 * Query Builder Set Data
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

use stdClass;

/**
 * Query Builder Set Data
 *
 * Sql - BuildSql - BuildSqlElements - BuildSqlGroups - SetData - EditData - FilterData - Base
 *
 * Collection of methods which process input data and store results in element-specific item arrays
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class SetData extends EditData
{
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
     * @since   1.0.0
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
        $this->editWhere($left, $condition);

        $item             = new stdClass();
        $item->group      = (string)trim($group);
        $item->left_item  = $this->setLeftRightConditionalItem($left_filter, $left);
        $item->condition  = $condition;
        $item->right_item = $this->setLeftRightConditionalItem($right_filter, $right, $condition);
        $item->connector  = $this->editConnector($connector);

        return $item;
    }

    /**
     * Set Conditions for Query - used for Where and Having
     *
     * @param   string $filter
     * @param   string $field
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function setLeftRightConditionalItem($filter = 'column', $field = '', $condition = '')
    {
        if ($filter === 'column') {
            return $this->setItem($field, 'column', null, null, null, false);
        }

        return $this->setItem($field, $filter, $field, null, $condition, true);
    }

    /**
     * Order By column name and optional value for alias
     *
     * @param   string      $column_name
     * @param   string      $type
     * @param   null|string $direction
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function setGroupByOrderBy($column_name, $type, $direction = 'ASC')
    {
        $this->editRequired($type, $column_name);

        $item = $this->setItem($column_name, 'column', null, null, null, true);

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
     * @return  string
     * @since   1.0.0
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
     * @return  stdClass
     * @since   1.0.0
     */
    protected function setItem($name, $data_type, $value = null, $alias = null, $condition = null, $filter = false)
    {
        if (trim($name) === '') {
            if (in_array($data_type, array('field', 'special'))) {
            } else {
                $name = 'value';
            }
        }

        $this->editRequired('name', $name);

        if ($data_type === 'special') {
            $name_and_prefix = array('prefix' => '', 'name' => $name);
        } else {
            $name_and_prefix = $this->setItemName($name);
        }

        $item            = new stdClass();
        $item->name      = $name_and_prefix['name'];
        $item->prefix    = $name_and_prefix['prefix'];
        $item->data_type = $this->setItemDataType($data_type);

        if ($value === null) {
            $item->value = null;
        } else {
            $item->value = $this->setItemValue($item->name, $data_type, $value, $condition, $filter);
        }

        if ($alias === null) {
            $item->alias = null;
        } else {
            $item->alias = $this->setItemAlias($alias);
        }

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
     * @since   1.0.0
     */
    protected function setItemValue($name, $data_type, $value = null, $condition = null, $filter = false)
    {
        if ($filter === false) {
            return $value;
        }

        if (strtoupper($condition) === 'IN') {
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
     * @since   1.0.0
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
     * @since   1.0.0
     */
    protected function setItemName($column_name)
    {
        if (strpos($column_name, '.')) {
            $temp        = explode('.', $column_name);
            $prefix      = (string)$temp[0];
            $column_name = (string)$temp[1];
        } else {
            $prefix = '';
        }

        return array('prefix' => $prefix, 'name' => $column_name);
    }

    /**
     * Set the Item data type
     *
     * @param   string $data_type
     *
     * @return  string
     * @since   1.0.0
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
     * @since   1.0.0
     */
    protected function setItemValueInDataType($value, $data_type)
    {
        $in_array = explode(',', $value);
        $value    = array();

        foreach ($in_array as $y) {
            $value[] = $this->filter('In array value', $y, $data_type);
        }

        return implode(',', $value);
    }
}
