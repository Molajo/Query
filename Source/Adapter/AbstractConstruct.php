<?php
/**
 * Abstract Construct Query
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Adapter;

use CommonApi\Database\DatabaseInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Model\FieldhandlerInterface;
use CommonApi\Query\QueryInterface;
use DateTime;
use Exception;
use stdClass;

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
     * @param   null|string $sql
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

        $string = '';

        foreach ($this->columns as $item) {

            if ($string == '') {
                $string = '(';
            } else {
                $string .= ', ';
            }

            $string .= trim($item->column);
        }

        $query .= $string . ')' . PHP_EOL;

        $string = '';

        foreach ($this->columns as $item) {

            if ($string == '') {
                $string = 'VALUES (';
            } else {
                $string .= ', ';
            }

            $string .= $item->value;
        }

        $query .= $string . ')' . PHP_EOL;

        return $query;
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
        $query .= $this->setWhere($query);
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

            if ($string == '') {
            } else {
                $string .= ', ' . PHP_EOL;
            }

            $string .= trim($item->column);

            if ($item->alias === null || trim($item->alias) == '') {
            } else {
                $string .= ' AS ' . $item->alias;
            }
        }

        return $string;
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
        if (is_array($this->group_by) && count($this->group_by) > 0) {
            return 'GROUP BY ' . $this->setGroupBySQL() . PHP_EOL;
        }

        return '';
    }

    /**
     * Generate SQL for Having
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectHaving()
    {
        if (count($this->having) > 0) {
            return 'HAVING ' . $this->setHavingSQL() . PHP_EOL;
        }

        return '';
    }

    /**
     * Generate SQL for Order By
     *
     * @return  string
     * @since   1.0
     */
    protected function setSQLSelectOrderBy()
    {
        if (is_array($this->order_by) && count($this->order_by) > 0) {
            return 'ORDER BY ' . $this->setOrderBySQL() . PHP_EOL;
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
        $this->editArray($this->columns, 'columns', true);

        $query = 'UPDATE ' . $this->setFromSQL() . PHP_EOL;

        $query .= 'SET ';

        $string = '';

        foreach ($this->columns as $key => $item) {

            $this->editDataType($item->data_type, $item->column);

            if ($string == '') {
            } else {
                $string .= ', ' . PHP_EOL;
            }

            $string .= trim($item->column) . ' = ' . $item->value;
        }

        $query .= $string . PHP_EOL;

        $query .= $this->setWhere($query);

        return $query;
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
     * @param   string $query
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

        foreach ($this->where_group as $where_group => $where_group_connector) {

            $string = '';

            foreach ($this->where as $where) {

                if (trim($where->group) == trim($where_group)) {

                    if (trim($string) == '') {
                    } else {
                        $string .= PHP_EOL . $where->connector . ' ';
                    }

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
            }

            if ($group_string == '') {
                if (trim($where_group) == '') {
                    $group_string .= $string . PHP_EOL;
                } else {
                    $group_string = '(';
                    $group_string .= $string . ')' . PHP_EOL;
                }

            } else {
                $group_string .= ' ' . $where_group_connector . ' (';
                $group_string .= $string . ')' . PHP_EOL;
            }
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

        foreach ($this->having_group as $having_group => $having_group_connector) {

            $string = '';

            foreach ($this->having as $having) {

                if (trim($having->group) == trim($having_group)) {

                    if (trim($string) == '') {
                    } else {
                        $string .= PHP_EOL . $having->connector . ' ';
                    }

                    $string .= $having->left . ' ' . $having->condition . ' ' . $having->right;
                }
            }

            if ($group_string == '') {
                if (trim($having_group) == '') {
                    $group_string .= $string . PHP_EOL;
                } else {
                    $group_string = '(';
                    $group_string .= $string . ')' . PHP_EOL;
                }

            } else {
                $group_string .= ' ' . $having_group_connector . ' (';
                $group_string .= $string . ')' . PHP_EOL;
            }
        }

        return $group_string;
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
