<?php
/**
 * Query Builder Build Sql Groups
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

/**
 * Query Builder Build Sql Groups
 *
 * Sql - BuildSql - BuildSqlElements - BuildSqlGroups - SetData - EditData - FilterData - Base
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class BuildSqlGroups extends SetData
{
    /**
     * Groups for 'AND' or 'OR' groups for both where and having
     *
     * @param   string $group
     * @param   string $group_connector
     * @param   string $type
     * @param   array  $group_array
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setGroup($group, $group_connector = 'AND', $type = 'WHERE', array $group_array = array())
    {
        $this->editRequired('group', $group);

        $group               = $this->filter($type, $group, 'string');
        $group_connector     = $this->editConnector($group_connector);
        $group_array[$group] = $group_connector;

        return $group_array;
    }

    /**
     * Generate SQL for Group Elements: where and having
     *
     * @param  array  $type_group_array Groups used separate where/having statements
     * @param  array  $type_array       Where/having statements
     * @param  string $type
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getGroups(
        array $type_group_array = array(),
        array $type_array = array(),
        $type = 'where'
    ) {
        $group_and_element_string = '';

        $groups = $this->initialiseGroups($type_group_array, $type);

        list($before, $after) = $this->getGroupsBeforeAfter($groups);

        foreach ($groups as $group => $group_connector) {
            $group_and_element_string
                = $this->getGroup($group_and_element_string, $before, $after, $group, $type_array, $group_connector);
        }

        if (trim($group_and_element_string) === '') {
            return '';
        }

        return $group_and_element_string . PHP_EOL;
    }

    /**
     * Process a single group
     *
     * @param  string $group_and_element_string
     * @param  string $before
     * @param  string $after
     * @param  string $group
     * @param  array  $type_array
     * @param  string $group_connector
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getGroup(
        $group_and_element_string,
        $before,
        $after,
        $group,
        array $type_array = array(),
        $group_connector = 'where'
    ) {
        $group_string = '';

        if (trim($group_and_element_string) === '') {
        } else {
            $group_string = strtoupper($group_connector);
        }

        $output = $this->getGroupItemsLoop($type_array, $group);

        if (trim($output) === '') {
        } else {
            $group_string .= $before;
            $group_string .= $this->getGroupItemsLoop($type_array, $group);

            $group_and_element_string .= trim($group_string) . $after;
        }

        return $group_and_element_string;
    }

    /**
     * Get group array - create single array entry if none exist
     *
     * @param   array $groups
     *
     * @return  string[]
     * @since   1.0.0
     */
    protected function getGroupsBeforeAfter($groups)
    {
        if (count($groups) === 1) {
            $before = '';
            $after  = '';
        } else {
            $before = '(';
            $after  = ')';
        }

        return array($before, $after);
    }

    /**
     * Get group array - create single array entry if none exist
     *
     * @param   mixed  $type_group_array
     * @param   string $type
     *
     * @return  array
     * @since   1.0.0
     */
    protected function initialiseGroups($type_group_array, $type)
    {
        $type_group_array = $this->editArray($type_group_array, $type, false);

        if (count($type_group_array) > 0) {
            return $type_group_array;
        }

        $type_group_array[''] = 'AND';

        return $type_group_array;
    }

    /**
     * Process Type Array for Group
     *
     * @param   array  $type_array
     * @param   string $group
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getGroupItemsLoop(array $type_array, $group)
    {
        $first_group_item = true;
        $group_string     = '';

        foreach ($type_array as $key => $item) {

            if ((string)trim($item->group) === (string)trim($group)) {
                $group_string     = $this->getGroupItem($item, $first_group_item, $group_string);
                $first_group_item = false;
            }
        }

        return $group_string;
    }

    /**
     * Generate SQL for Group Item
     *
     * @param   object  $item
     * @param   boolean $first_group_item
     * @param   string  $group_string
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getGroupItem($item, $first_group_item, $group_string)
    {
        $sql = $this->getQuoteLeftRight($item->left_item);
        $sql .= ' ' . $item->condition . ' ';
        $sql .= $this->getQuoteLeftRight($item->right_item, $item->condition);

        if ($first_group_item === true) {
            $group_string = $sql;

        } else {
            $group_string .= PHP_EOL . '    ' . strtoupper($item->connector) . ' ' . $sql;
        }

        return $group_string;
    }

    /**
     * Generate SQL for Group Item
     *
     * @param   object      $item
     * @param   null|string $condition
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getQuoteLeftRight($item, $condition = null)
    {
        if (strtoupper($condition) === 'IN') {
            return ' (' . $this->getQuoteList($item->value, 2) . ')';
        }

        if ($item->data_type === 'column') {
            return $this->quoteNameAndPrefix($item->name, $item->prefix);
        }

        return $this->quoteValue($item->value);
    }

    /**
     * Generate Data needed for SQL List - escaped prior to this point
     *
     * @param   string  $value_array
     * @param   integer $key_value      0: Only use $value
     *                                  1: key=value
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getQuoteList($value_array, $key_value = 0)
    {
        $sql = '';

        $x = explode(',', $value_array);

        foreach ($x as $value) {
            $sql = $this->getLoopList($key_value, $sql, $this->quoteValue($value));
        }

        return $sql;
    }

    /**
     * Generate Data needed for SQL List - escaped prior to this point
     *
     * @param   array   $value_array
     * @param   integer $key_value      0: Only use $value
     *                                  1: key=value
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getLoop(array $value_array = array(), $key_value = 0)
    {
        $sql = '';

        if ($key_value === 0) {
            foreach ($value_array as $value) {
                $sql = $this->getLoopList($key_value, $sql, $value);
            }
        } else {
            foreach ($value_array as $key => $value) {
                $sql = $this->getLoopList($key_value, $sql, $value, $key);
            }
        }

        return $sql;
    }

    /**
     * Render the SQL
     *
     * @param   string $key_value 0: only use $value
     *                            1: key=value
     *                            2: list
     * @param   string $sql
     * @param   string $value
     * @param   string $key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getLoopList($key_value, $sql, $value, $key = null)
    {
        if ($sql === '') {
        } elseif ($key_value === 2) {
            $sql .= ', ';
        } else {
            $sql .= ', ' . PHP_EOL . '    ';
        }

        if ($key_value === 1) {
            $sql .= trim($key) . ' = ' . $value;
        } else {
            $sql .= trim($value);
        }

        return $sql;
    }
}
