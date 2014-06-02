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
            'getColumns' => array('columns', false, true, '', '', 'COLUMNS'),
            'getFrom'    => array('from', true, false, '', '', 'FROM'),
            'getWhere'   => array('where', false, true, '', 'where', 'WHERE'),
            'getGroupBy' => array('group_by', true, false, '', '', 'GROUP BY'),
            'getHaving'  => array('having', false, true, '', 'having', 'HAVING'),
            'getOrderBy' => array('order_by', true, false, '', '', 'ORDER BY')
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
        $query = 'INSERT INTO ' . $this->getFrom() . PHP_EOL;
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

        $query_string = 'UPDATE ' . $this->getFrom() . PHP_EOL;
        $query_string .= 'SET ' . $this->getLoop($array, 1, 2) . PHP_EOL;
        $query_string .= $this->getWhere();

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
        $query = 'DELETE FROM ' . $this->getFrom() . PHP_EOL;
        $query .= $this->getWhere();

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
        $query = $this->getDistinct();

        foreach ($this->groups_array as $key => $value) {
            return $this->getElement($key);
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
     * Generate Column SQL
     *
     * @return string
     * @since 1.0
     */
    protected function getColumns()
    {
        return $this->getElement('getColumns');
    }

    /**
     * Generate FROM SQL
     *
     * @return string
     * @since 1.0
     */
    protected function getFrom()
    {
        return $this->getElement('getFrom');
    }

    /**
     * Generate FROM SQL
     *
     * @return string
     * @since 1.0
     */
    protected function getWhere()
    {
        return $this->getElement('getWhere');
    }

    /**
     * Generate FROM SQL
     *
     * @return string
     * @since 1.0
     */
    protected function getHaving()
    {
        return $this->getElement('getHaving');
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
        $type_array = $this->groups_array[$type];

        $key_value = 1;
        $option    = 1;

        $array = $this->getElementsArray(
            $this->$type_array[0],
            $type_array[1], // get_value
            $type_array[2], // get_column
            $type_array[3] // use_alias
        );

        if ($type === 'where' || $type === 'having') {
            return $this->getGroups(
                $array, // element_type_array
                $this->$type_array[0], // element_array
                $type_array[4]
            ) // type
            . PHP_EOL;
        }

        return trim(
            $type_array[5]
            . ' '
            . $this->getLoop($array, $key_value, $option)
        ) . PHP_EOL;
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
            return 'LIMIT ' . $this->offset . ', ' . $this->limit . PHP_EOL;
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
