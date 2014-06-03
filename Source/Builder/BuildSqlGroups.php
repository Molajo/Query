<?php
/**
 * Query Builder Build Sql Groups
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

/**
 * Query Builder Build Sql Groups
 *
 * Sql - BuildSql - BuildSqlGroups - BuildSqlElements - SetData - EditData - FilterData - Base
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class BuildSqlGroups extends BuildSqlElements
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
     * @since   1.0
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
     * @since   1.0
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
        }

        if (trim($group_and_element_string) === '') {
            return '';
        }

        return $group_and_element_string . PHP_EOL;
    }

    /**
     * Get group array - create single array entry if none exist
     *
     *
     * @return  string[]
     * @since   1.0
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
     * @since   1.0
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
     *
     * @return  string
     * @since   1.0
     */
    protected function getGroupItemsLoop($type_array, $group)
    {
        $first_group_item = true;
        $group_string     = '';

        foreach ($type_array as $key => $item) {

            if ((string)trim($item->group) === (string)trim($group)) {

                $sql = $this->getGroupItem($item);

                if ($first_group_item === true) {
                    $first_group_item = false;
                    $group_string     = $sql;
                } else {
                    $group_string .= PHP_EOL . '    ' . strtoupper($item->connector) . ' ' . $sql;
                }
            }
        }

        return $group_string;
    }

    /**
     * Generate SQL for Group Item
     *
     * @param   object $item
     *
     * @return  string
     * @since   1.0
     */
    protected function getGroupItem($item)
    {
        $sql = $item->left_item->prefix . $this->quoteName($item->left_item->name);

        $sql .= ' ' . strtoupper($item->condition) . ' ';

        if (strtoupper($item->condition) === 'IN') {
            $sql .= ' (' . $this->getLoop($item->right_item->value, 0, 2) . ')';

        } elseif ($item->right_item->value === null) {
            $sql .= $item->left_item->prefix . $this->quoteName($item->right_item->name);

        } else {
            $sql .= $item->right_item->value;
        }

        return $sql;
    }

    /**
     * Generate Data needed for SQL List
     *
     * @param   array   $value_array
     * @param   integer $key_value
     * @param   integer $option
     *
     * @return  string
     * @since   1.0
     */
    protected function getLoop(array $value_array = array(), $key_value = 0, $option = 0)
    {
        $sql = '';

        if ($key_value === 0) {
            foreach ($value_array as $value) {
                $sql = $this->getLoopList($option, $sql, $value);
            }
        } else {
            foreach ($value_array as $key => $value) {
                $sql = $this->getLoopList($option, $sql, $value, $key);
            }
        }

        return $sql;
    }

    /**
     * Render the SQL
     *
     * @param   string $option 1: comma delimited list
     *                         2: comma delimited quoted list
     *                         3: key=value
     * @param   string $sql
     * @param   string $value
     * @param   string $key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getLoopList($option, $sql, $value, $key = null)
    {
        if ($sql === '') {
        } else {
            $sql .= ', ' . PHP_EOL . '    ';
        }

        if ($option === 1) {
            $sql .= trim($value);
        } elseif ($option === 2) {
            $sql .= $this->quoteValue($value);
        } else {
            $sql .= trim($key) . ' = ' . $value;
        }

        return $sql;
    }
}
