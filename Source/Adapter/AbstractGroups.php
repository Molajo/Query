<?php
/**
 * Abstract Query Group Class
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Adapter;

/**
 * Abstract Query Group Class
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class AbstractGroups extends AbstractAdapter
{
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

        foreach ($this->initialiseGroups($type_group_array, $type) as $group => $group_connector) {

            // Begin Group
            $group_string = '';

            if (trim($group_string) === '') {
            } else {
                $group_string = strtoupper($group_connector);
            }

            $group_string .= ' (';
            $first_group_item = true;

            // Group Item Loop
            $group_string .= $this->getGroupItemsLoop($type_array, $group);

            // End Group
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

        return $this->setGroup('', 'AND', $type, $type_group_array);
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

        if (strtolower($item->condition) === 'in') {
            $sql .= ' ' . $this->getGroupItemInCondition($item->right_item);
        } else {
            $sql .= ' ' . $this->quoteValue($item->right_item);
        }

        return $sql . PHP_EOL;
    }

    /**
     * Generate SQL for IN (condition)
     *
     * @param   array $values
     *
     * @return  string
     * @since   1.0
     */
    protected function getGroupItemInCondition(array $values = array())
    {
        $sql = '';

        foreach ($values as $value) {
            if ($sql === '') {
            } else {
                $sql .= ', ';
            }

            $sql .= $this->quoteValue($value);
        }

        return ' (' . trim($sql) . ')';
    }

    /**
     * Generate SQL for Loop
     *
     * @param   array   $value_array
     * @param   string  $type
     * @param   bool    $exception
     * @param   integer $template
     *
     * @return  string
     * @since   1.0
     */
    protected function getLoop(array $value_array = array(), $type = 'where', $exception = false, $template = 1)
    {
        if ($this->editArray($value_array, $type, $exception) === false) {
            return '';
        };

        if ($template === 1) {
            return $this->getLoopCommaDelimitedList($value_array);
        }

        return $this->getLoopKeyEqualValuePairs($value_array);
    }

    /**
     * Creates a comma delimited list of already escaped or filtered values
     *  used for 'select', 'from', 'group by' and 'order by'
     *
     * @param   array $value_array
     *
     * @return  string
     * @since   1.0
     */
    protected function getLoopCommaDelimitedList(array $value_array = array())
    {
        $query_string = '';

        foreach ($value_array as $value) {

            if ($query_string === '') {
            } else {
                $query_string .= ', ' . PHP_EOL;
            }

            $query_string .= trim($value);
        }

        return $query_string;
    }

    /**
     * Creates a comma delimited list of already escaped and filtered values - used with Update
     *
     * @param   array $value_array
     *
     * @return  string
     * @since   1.0
     */
    protected function getLoopKeyEqualValuePairs(array $value_array = array())
    {
        $query_string = '';

        foreach ($value_array as $key => $value) {

            if ($query_string === '') {
            } else {
                $query_string .= ', ' . PHP_EOL;
            }

            $query_string .= trim($key) . ' = ' . $value;
        }

        return $query_string;
    }
}
