<?php
/**
 * Query Builder Groups
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

/**
 * Query Builder Groups
 *
 * Base - Filters - Edits - Elements - Groups - Generate - Sql
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Groups extends Elements
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
        $type = 'WHERE'
    ) {
        $group_and_element_string = '';

        foreach ($this->initialiseGroups($type_group_array, $type) as $group => $group_connector) {
            $group_string = '';
            if (trim($group_string) === '') {
            } else {
                $group_string = strtoupper($group_connector);
            }

            $group_string .= ' (';
            $group_string .= $this->getGroupItemsLoop($type_array, $group);
            $group_and_element_string .= $group_string . ')';
        }

        return $group_and_element_string;
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
     * @param   mixed  $type_group_array
     * @param   string $type
     *
     * @return  array
     * @since   1.0
     */
    protected function getGroupItemsLoop($type_array, $group)
    {
        $first_group_item = true;
        $group_string = '';

        foreach ($type_array as $key => $item) {

            if ((string)trim($item->group) === (string)trim($group)) {

                $sql = $this->getGroupItem($item);

                if ($first_group_item === true) {
                    $first_group_item = false;
                } else {
                    $sql .= ' ' . strtoupper($item->connector) . ' ';
                }

                $group_string .= $sql;
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
        $sql = $this->quoteName($item->left_item);

        $sql .= ' ' . strtoupper($item->condition);

        if (strtoupper($item->condition) === 'IN') {
            $sql .= ' (' .  $this->getLoop($item->right_item, 0, 2) . ')';
        } else {
            $sql .= ' ' . $this->quoteValue($item->right_item);
        }

        return $sql . PHP_EOL;
    }

    /**
     * Generate Data needed for SQL List
     *
     * @param   array   $value_array
     * @param   string  $key_value
     * @param   string  $option
     *
     * @return  string
     * @since   1.0
     */
    protected function getLoop(array $value_array = array(), $key_value = 0, $option = 0)
    {
        $sql = '';

        if ($key_value === 0) {
            foreach ($value_array as $value) {
                $sql .= $this->getLoopList($option, $sql, $value);
            }
        } else {
            foreach ($value_array as $key => $value) {
                $sql .= $this->getLoopList($option, $sql, $value, $key);
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
     * @param   string  $sql
     * @param   string  $value
     * @param   string  $key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getLoopList($option, $sql, $value, $key = null)
    {
        if ($sql === '') {
        } else {
            $sql .= ', ' . PHP_EOL;
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
