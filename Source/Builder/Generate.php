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

        $array = $this->getElementsArray($this->columns, $get_value, $get_column, false);

        return $start . $this->getLoop($array, 1, 1) . $end . PHP_EOL;
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
        $query .= $this->getColumns();
        $query .= $this->getFrom();
        $query .= $this->getWhere();
        $query .= $this->getGroupBy();
        $query .= $this->getHaving();
        $query .= $this->getOrderBy();
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
     * @return  string
     * @since   1.0
     */
    protected function getColumns()
    {
        $array = $this->getElementsArray($this->columns, false, true, true);

        return $this->getLoop($array, 1, 1) . PHP_EOL;
    }

    /**
     * Generate FROM SQL
     *
     * @return  string
     * @since   1.0
     */
    protected function getFrom()
    {
        $array = $this->getElementsArray($this->from, true, false, true);

        return 'FROM ' . $this->getLoop($array, 1, 1) . PHP_EOL;
    }

    /**
     * Generate WHERE SQL
     *
     * @return  string
     * @since   1.0
     */
    protected function getWhere()
    {
        $array = $this->getElementsArray($this->where, true, false, true);

        return 'WHERE ' . $this->getGroups($array, $this->where, 'where') . PHP_EOL;
    }

    /**
     * Generate GROUP BY SQL
     *
     * @return  string
     * @since   1.0
     */
    protected function getGroupBy()
    {
        $array = $this->getElementsArray($this->group_by, true, false, false);

        return 'GROUP BY ' . $this->getLoop($array, 1, 1) . PHP_EOL;
    }

    /**
     * Generate HAVING SQL
     *
     * @return  string
     * @since   1.0
     */
    protected function getHaving()
    {
        $array = $this->getElementsArray($this->having, true, false, false);

        return 'HAVING ' . $this->getGroups($array, $this->having, 'having') . PHP_EOL;
    }

    /**
     * Generate ORDER BY SQL
     *
     * @return  string
     * @since   1.0
     */
    protected function getOrderBy()
    {
        $array = $this->getElementsArray($this->order_by, true, false, false);

        return 'ORDER BY ' . $this->getLoop($array, 1, 1) . PHP_EOL;
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
