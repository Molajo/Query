<?php
/**
 * Query Builder Generate
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

/**
 * Query Builder Generate
 *
 * Base - Filters - Utilities - Groups - Generate - Sql
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Generate extends Groups
{
    /**
     * Groups array for processing
     *
     * @var    array
     * @since  1.0.0
     */
    protected $groups_array
        = array(
            'columns'  => array(
                'type'            => 'columns',
                'get_value'       => false,
                'get_column'      => true,
                'use_alias'       => true,
                'group_connector' => '',
                'return_literal'  => '',
                'key_value'       => 0,
                'format'          => 1
            ),
            'from'     => array(
                'type'            => 'from',
                'get_value'       => false,
                'get_column'      => true,
                'use_alias'       => true,
                'group_connector' => '',
                'return_literal'  => 'FROM',
                'key_value'       => 0,
                'format'          => 1
            ),
            'where'    => array(
                'type'           => 'where',
                'get_value'      => false,
                'get_column'     => true,
                'use_alias'      => false,
                'connector'      => 'AND',
                'return_literal' => 'WHERE',
                'key_value'      => 1,
                'format'         => 1
            ),
            'order_by' => array(
                'type'           => 'order_by',
                'get_value'      => false,
                'get_column'     => true,
                'use_alias'      => false,
                'connector'      => '',
                'return_literal' => 'ORDER BY ',
                'key_value'      => 0,
                'format'         => 1
            ),
           'having'   => array(
                'type'           => 'having',
                'get_value'      => false,
                'get_column'     => true,
                'use_alias'      => false,
                'connector'      => 'AND',
                'return_literal' => 'HAVING',
                'key_value'      => 1,
                'format'         => 1
            ),
            'group_by' => array(
                'type'           => 'group_by',
                'get_value'      => false,
                'get_column'     => true,
                'use_alias'      => false,
                'connector'      => '',
                'return_literal' => 'GROUP BY ',
                'key_value'      => 0,
                'format'         => 1
            )
        );

    /**
     * Use externally provided SQL
     *
     * @param   null|string $sql
     *
     * @return  $this
     * @since   1.0
     */
    protected function getExternalSql($sql = null)
    {
        $this->sql = $this->getDatabasePrefix($sql);

        return $this;
    }

    /**
     * Generate SQL
     *
     * @return  $this
     * @since   1.0
     */
    protected function generateSql()
    {
        if (strtolower($this->query_type) === 'delete') {
        } else {
            $this->columns = $this->editArray($this->columns, 'columns', true);
        }

        $model     = 'get' . ucfirst(strtolower($this->query_type));
        $query     = $this->$model();
        $this->sql = $this->getDatabasePrefix($query);

        return $this;
    }

    /**
     * INSERT
     *
     * @return  string
     * @since   1.0
     */
    protected function getInsert()
    {
        $query = 'INSERT INTO ' . $this->getElement('from') . PHP_EOL;
        $query .= $this->getInsertColumnsValues('column', 'VALUES (', ')');
        $query .= $this->getInsertColumnsValues('value', ' (', ')');

        return $query;
    }

    /**
     * Generate SQL for Insert Column Values
     *
     * @param   string $type
     * @param   string $start
     * @param   string $end
     *
     * @return  string
     * @since   1.0
     */
    protected function getInsertColumnsValues($type, $start = 'VALUES (', $end = ')')
    {
        if ($type === 'value') {
            $get_value  = true;
            $get_column = false;
        } else {
            $get_value  = false;
            $get_column = true;
        }

        return $this->getElement($this->columns, $get_value, $get_column, false, 1, 1);
    }

    /**
     * Generate Insert From SQL
     *
     * @return  string
     * @since   1.0
     */
    protected function getInsertfrom()
    {
        return '';
    }

    /**
     * UPDATE
     *
     * Generate SQL for Update
     *
     * @return  string
     * @since   1.0
     */
    protected function getUpdate()
    {
        $array = $this->getElementsArray($this->columns, true, true, false);

        $query_string = 'UPDATE ' . $this->getElement('from') . PHP_EOL;
        $query_string .= 'SET ' . $this->getLoop($array, 1, 2) . PHP_EOL;
        $query_string .= $this->getElement('where');

        return $query_string;
    }

    /**
     * DELETE
     *
     * Generate SQL for Delete
     *
     * @return  string
     * @since   1.0
     */
    protected function getDelete()
    {
        $query = 'DELETE FROM ' . $this->getElement('from') . PHP_EOL;
        $query .= $this->getElement('where');

        return $query;
    }

    /**
     * SELECT
     *
     * Generate SQL for Select
     *
     * @return  string
     * @since   1.0
     */
    protected function getSelect()
    {
        $query = '';

        foreach ($this->groups_array as $key => $value) {
            $new = $this->getElement($key);
            if (trim($query) === '') {
                $query = $this->getDistinct() . $new;
            } else {
                $query .= PHP_EOL . $new;
            }
        }

        $query .= $this->getLimit();

        return $query;
    }

    /**
     * Generate SQL for SELECT DISTINCT
     *
     * @return  string
     * @since   1.0
     */
    protected function getDistinct()
    {
        if ($this->distinct === true) {
            return 'SELECT DISTINCT ';
        }

        return 'SELECT ';
    }

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
        $a = $this->groups_array[$type];

        if ($type === 'where' || $type === 'having') {
            $t = $type . '_group';
            $output = $this->getGroups($this->$t, $this->$type, $a['connector']);
        } else {
            $array = $this->getElementsArray($this->$type, $a['get_value'], $a['get_column'], $a['use_alias']);
            $output = $this->getLoop($array, $a['key_value'], $a['format']);
        }

        return $this->returnGetElement($a['return_literal'], $output);
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
     * Generate SQL for Limit
     *
     * @return  string
     * @since   1.0
     */
    protected function getLimit()
    {
        if ((int)$this->offset === 0 && (int)$this->limit === 0) {
        } else {
            return PHP_EOL . 'LIMIT ' . $this->offset . ', ' . $this->limit;
        }

        return '';
    }

    /**
     * Set Database Prefix
     *
     * @param   string $query
     *
     * @return  string
     * @since   1.0
     */
    protected function getDatabasePrefix($query)
    {
        return str_replace('#__', $this->database_prefix, $query);
    }
}
