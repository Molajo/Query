<?php
/**
 * Abstract Construct Query
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Adapter;

use CommonApi\Query\QueryInterface;

/**
 * Abstract Construct Query
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class AbstractConstruct extends AbstractCollect implements QueryInterface
{
    /**
     * Get SQL (optionally setting the SQL)
     *
     * @param   null|string $sql
     *
     * @return  string
     * @since   1.0
     */
    public function getSQL($sql = null)
    {
        if ($sql === null || trim($sql) == '') {
            $this->getSQLGenerate();
        } else {
            $this->sql = '';
            $this->getSQLExternal($sql);
        }

        return $this->sql;
    }

    /**
     * Get SQL (optionally setting the SQL)
     *
     * @param   null|string $sql
     *
     * @return  $this
     * @since   1.0
     */
    public function getSQLExternal($sql = null)
    {
        $this->sql = $this->setDatabasePrefix($sql);

        return $this;
    }

    /**
     * Generate SQL
     *
     *
     * @return  $this
     * @since   1.0
     */
    public function getSQLGenerate()
    {
        $model     = 'setSQL' . ucfirst(strtolower($this->query_type));
        $query     = $this->$model();
        $this->sql = $this->setDatabasePrefix($query);

        return $this;
    }

    /**
     * Generate SQL for Insert
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLInsert()
    {
        $query = 'INSERT INTO ' . $this->setFromSQL() . PHP_EOL;
        $query .= $this->setSQLInsertColumns();
        $query .= $this->setSQLInsertValues();

        return $query;
    }

    /**
     * Generate SQL for Insert Columns
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLInsertColumns()
    {
        $string = '';
        foreach ($this->columns as $item) {

            if ($string == '') {
                $string = '(';
            } else {
                $string .= ', ';
            }

            $string .= trim($item->column);
        }

        return $string . ')' . PHP_EOL;
    }

    /**
     * Generate SQL for Insert Column Values
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLInsertValues()
    {
        $string = '';

        foreach ($this->columns as $item) {

            if ($string == '') {
                $string = 'VALUES (';
            } else {
                $string .= ', ';
            }

            $string .= $item->value;
        }

        return $string . ')' . PHP_EOL;
    }

    /**
     * Generate SQL for Insert using a select
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLInsertfrom()
    {
        return '';
    }

    /**
     * Generate SQL for Select
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setSQLSelect()
    {
        $this->editArray($this->columns, 'columns', true);

        $string = $this->setSQLSelectDistinct();
        $string .= $this->setSQLSelectColumns();
        $query = $string . PHP_EOL;
        $query .= $this->setSQLSelectFrom();
        $query .= $this->setWhere();
        $query .= $this->setSQLSelectGroupBy();
        $query .= $this->setSQLSelectHaving();
        $query .= $this->setSQLSelectOrderBy();
        $query .= $this->setSQLSelectLimit();

        return $query;
    }

    /**
     * Generate SQL for Select
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectDistinct()
    {
        if ($this->distinct === true) {
            return 'SELECT DISTINCT ';
        }
        return 'SELECT ';
    }

    /**
     * Generate SQL for Columns
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectColumns()
    {
        $string = '';

        foreach ($this->columns as $item) {
            $string = $this->setSQLSelectColumnsConnector($string);
            $string .= trim($item->column);
            $string .= $this->setSQLSelectColumnsAlias($item->alias);
        }

        return $string;
    }

    /**
     * Generate SQL for Column Connector
     *
     * @param string $string
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectColumnsConnector($string)
    {
        if ($string == '') {
        } else {
            $string .= ', ' . PHP_EOL;
        }

        return $string;
    }

    /**
     * Generate SQL for Column Alias
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectColumnsAlias($alias)
    {
        if ($alias === null || trim($alias) == '') {
            return '';
        }

        return ' AS ' . $alias;
    }

    /**
     * Generate SQL for From
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectFrom()
    {
        return 'FROM ' . $this->setFromSQL() . PHP_EOL;
    }

    /**
     * Generate SQL for Group By
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectGroupBy()
    {
        return $this->setSQLSelectOrderGroupHaving('group_by', 'GROUP BY', 'setGroupBySQL');
    }

    /**
     * Generate SQL for Having
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectHaving()
    {
        return $this->setSQLSelectOrderGroupHaving('having', 'HAVING', 'setHavingSQL');
    }

    /**
     * Generate SQL for Order By
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectOrderBy()
    {
        return $this->setSQLSelectOrderGroupHaving('order_by', 'ORDER BY', 'setOrderBySQL');
    }

    /**
     * Generate SQL for Order By or Group By
     *
     * @param string $field_name
     * @param string $literal
     * @param string $method
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectOrderGroupHaving($field_name, $literal, $method)
    {
        if (is_array($this->$field_name) && count($this->$field_name) > 0) {
            return $literal . ' ' . $this->$method() . PHP_EOL;
        }

        return '';
    }

    /**
     * Generate SQL for Limit
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectLimit()
    {
        if ((int)$this->offset == 0 && (int)$this->limit == 0) {
        } else {
            return 'LIMIT ' . $this->setSQLLimit();
        }

        return '';
    }

    /**
     * Generate SQL for Update
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLUpdate()
    {
        $query = 'UPDATE ' . $this->setFromSQL() . PHP_EOL;
        $query .= 'SET ';
        $string = $this->setSQLUpdateColumns();
        $query .= $string . PHP_EOL;
        $query .= $this->setWhere();

        return $query;
    }

    /**
     * Generate SQL for Update
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLUpdateColumns()
    {
        $this->editArray($this->columns, 'columns', true);

        $string = '';

        foreach ($this->columns as $key => $item) {

            $this->editDataType($item->data_type, $item->column);

            if ($string == '') {
            } else {
                $string .= ', ' . PHP_EOL;
            }

            $string .= trim($item->column) . ' = ' . $item->value;
        }

        return $string;
    }

    /**
     * Generate SQL for Delete
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLDelete()
    {
        $query = 'DELETE FROM ' . $this->setFromSQL() . PHP_EOL;
        $query .= $this->setWhere();

        return $query;
    }

    /**
     * Generate SQL for Where
     *
     *
     * @return  string
     * @since   1.0
     */
    protected function setWhere()
    {
        if (count($this->where) > 0) {
            return 'WHERE ' . $this->setWhereSQL() . PHP_EOL;
        }

        return '';
    }

    /**
     * Generate SQL for Columns
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setFromSQL()
    {
        $this->editArray($this->from, 'from', true);

        return $this->setLoopSQL($this->from);
    }

    /**
     * Generate SQL for Where Conditions
     *
     * @return  string
     * @since   1.0
     */
    protected function setWhereSQL()
    {
        if ($this->editArray($this->where, 'where', false) === false) {
            return '';
        }

        if (count($this->where_group) > 0) {
        } else {
            $this->where_group     = array();
            $this->where_group[''] = 'AND';
        }

        $group_string = '';

        foreach ($this->where_group as $group => $group_connector) {
            $group_string = $this->setSQLGroup($group_string, $group, $group_connector, 'where');
        }

        return $group_string;
    }

    /**
     * Generate SQL for Group By
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setGroupBySQL()
    {
        if ($this->editArray($this->group_by, 'group_by', false) === false) {
            return '';
        }

        return $this->setLoopSQL($this->group_by);
    }

    /**
     * Generate SQL for Having Conditions
     *
     * @return  string
     * @since   1.0
     */
    protected function setHavingSQL()
    {
        if ($this->editArray($this->having, 'having', false) === false) {
            return '';
        };

        if (count($this->having_group) > 0) {
        } else {
            $this->having_group     = array();
            $this->having_group[''] = 'AND';
        }

        $group_string = '';

        foreach ($this->having_group as $group => $group_connector) {
            $group_string = $this->setSQLGroup($group_string, $group, $group_connector, 'having');
        }

        return $group_string;
    }

    /**
     * Generate SQL for Where Conditions
     *
     * @param string $group_string
     * @return  string
     * @since   1.0
     */
    protected function setSQLGroup($group_string, $group, $where_group_connector, $data = 'where')
    {
        $string = '';

        foreach ($this->$data as $value) {
            $method = 'setSQL' . ucfirst(strtolower($data)) . 'Group';
            $string = $this->$method($string, $value, $group);
        }

        if ($group_string == '') {
            if (trim($group) == '') {
                $group_string .= $string . PHP_EOL;
            } else {
                $group_string = '(';
                $group_string .= $string . ')' . PHP_EOL;
            }

        } else {
            $group_string .= ' ' . $where_group_connector . ' (';
            $group_string .= $string . ')' . PHP_EOL;
        }

        return $group_string;
    }

    /**
     * Generate SQL for Having Group
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLWhereGroup($string, $where, $where_group)
    {
        if (trim($where->group) == trim($where_group)) {

            $string = $this->setSQLGroupBeginning($string, $where->connector);

            $string .= $where->left . ' ' . strtoupper($where->condition);

            if (strtolower($where->condition) == 'in') {
                $in_string = '';

                foreach ($where->right as $value) {

                    if ($in_string == '') {
                    } else {
                        $in_string .= ', ';
                    }

                    $in_string .= $value;
                }

                $where->right = '(' . trim($in_string) . ')';
            }

            $string .= ' ' . $where->right;
        }

        return $string;
    }

    /**
     * Generate SQL for Having Group
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLHavingGroup($string, $having, $having_group)
    {
        if (trim($having->group) == trim($having_group)) {
            $string .= $this->setSQLGroupBeginning($string, $having->connector);
            $string .= $having->left . ' ' . $having->condition . ' ' . $having->right;
        }

        return $string;
    }

    /**
     * Generate SQL for Having Group
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setSQLGroupBeginning($string, $connector)
    {
        if (trim($string) == '') {
        } else {
            $string .= PHP_EOL . $connector . ' ';
        }

        return $string;
    }

    /**
     * Generate SQL for Order By
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setOrderBySQL()
    {
        if ($this->editArray($this->order_by, 'order_by', false) === false) {
            return '';
        };

        return $this->setLoopSQL($this->order_by);
    }

    /**
     * Generate SQL for Select
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLLimit()
    {
        return $this->offset . ', ' . $this->limit . PHP_EOL;
    }

    /**
     * Set Database Prefix
     *
     * @param   string $query
     *
     * @return  string
     * @since   1.0
     */
    protected function setDatabasePrefix($query)
    {
        return str_replace('#__', $this->database_prefix, $query);
    }

    /**
     * Generate SQL for Loop
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setLoopSQL($set)
    {
        $string = '';

        foreach ($set as $value) {

            if ($string == '') {
            } else {
                $string .= ', ' . PHP_EOL;
            }

            $string .= trim($value);
        }

        return $string;
    }
}
