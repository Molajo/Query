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
        $groups_array = array(
            'COLUMNS'  => array($this->from, true, false, ''),
            'FROM'     => array($this->from, true, false, ''),
            'WHERE'    => array($this->columns, false, true, '', 'where'),
            'ORDER BY' => array($this->order_by, false, true, ''),
            'GROUP BY' => array($this->group_by, true, false, ''),
            'HAVING'   => array($this->having, false, true, '', 'having'),
        );

        $query = $this->getDistinct();

        foreach ($groups_array as $key => $value) {
            $query .= $key . $this->getElement($value[0], $value[1], $value[2], true, 1, 1) . PHP_EOL;
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
     * @param   array   $element_array
     * @param   boolean $get_value
     * @param   boolean $get_column
     * @param   boolean $use_alias
     * @param   integer $key_value
     * @param   integer $option
     *
     * @return  string
     * @since   1.0
     */
    protected function getElement(
        array $element_array = array(),
        $get_value = false,
        $get_column = true,
        $use_alias = true,
        $key_value = 0,
        $option = 0,
        $type = ''
    ) {
        $array = $this->getElementsArray($element_array, $get_value, $get_column, $use_alias);

        if ($type === 'where' || $type === 'having') {
            return $this->getGroups($array, $element_array, $type) . PHP_EOL;
        }

        return $this->getLoop($array, $key_value, $option) . PHP_EOL;
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
