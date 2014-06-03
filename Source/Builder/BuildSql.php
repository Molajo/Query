<?php
/**
 * Query Builder Generate Sql
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

/**
 * Query Builder Generate Sql
 *
 * Sql - BuildSql - BuildSqlElements - BuildSqlGroups - SetData - EditData - FilterData - Base
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class BuildSql extends BuildSqlElements
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

        $query     = $this->{'get' . ucfirst(strtolower($this->query_type))}();
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
        return $this->getInsertType('values');
    }

    /**
     * Generate Insert From SQL
     *
     * @return  string
     * @since   1.0
     */
    protected function getInsertfrom()
    {
        return $this->getInsertType('from');
    }

    /**
     * Generate Insert From SQL
     *
     * @return  string
     * @since   1.0
     */
    protected function getInsertType($type)
    {
        $query = 'INSERT INTO ' . $this->getElement('insert_into_table') . PHP_EOL;
        $query .= $this->getElement('columns') . PHP_EOL;
        if ($type === 'from') {
            $query .= $this->getSelect();
            $this->sql = $query;
        } else {
            $this->sql .= $this->getElement('values');
        }

        return $this->sql;
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
        $query_string = 'UPDATE ' . $this->getElement('from') . PHP_EOL;
        $query_string .= 'SET ' . $this->getElement('update_columns') . PHP_EOL;

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
        $this->sql = '';

        foreach ($this->select_array as $key) {
            $new_sql  = $this->getElement($key);
            $this->sql = $this->getSelectAppend($new_sql, $this->sql);
        }

        return $this->sql;
    }

    /**
     * Append results only for those elements with generated SQL
     *
     * @param   string $new_sql
     * @param   string $full_sql
     *
     * @return  string
     * @since   1.0
     */
    protected function getSelectAppend($new_sql = '', $full_sql = '')
    {
        if (trim($new_sql) === '') {
            return $full_sql;
        }

        if (trim($full_sql) === '') {
            $full_sql = $this->getDistinct() . $new_sql;
        } else {
            $full_sql .= PHP_EOL . $new_sql;
        }

        return $full_sql;
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
