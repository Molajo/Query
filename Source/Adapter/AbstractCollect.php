<?php
/**
 * Abstract Collect Query Elemenets
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Adapter;

use CommonApi\Exception\RuntimeException;
use CommonApi\Query\QueryInterface;
use stdClass;

/**
 * Abstract Collect Query Elements
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class AbstractCollect extends AbstractAdapter implements QueryInterface
{

    /**
     * Set Query Type
     *
     * @param   string $query_type
     *
     * @return  $this
     * @since   1.0
     */
    public function setType($query_type = 'select')
    {
        $query_type = strtolower($query_type);

        if ($query_type == 'insert'
            || $query_type == 'insert-from'
            || $query_type == 'select'
            || $query_type == 'update'
            || $query_type == 'delete'
            || $query_type == 'exec'
        ) {
            $this->query_type = $query_type;
        } else {
            $this->query_type = 'select';
        }

        return $this;
    }

    /**
     * Set Distinct Indicator
     *
     * @param   boolean $distinct
     *
     * @return  $this
     * @since   1.0
     */
    public function setDistinct($distinct = false)
    {
        if ($distinct === true) {
            $this->distinct = true;
        } else {
            $this->distinct = false;
        }

        return $this;
    }

    /**
     * Used for select, insert, and update to specify column name, alias (optional)
     *  For Insert and Update, only, value and data_type
     *
     * @param   string      $column_name
     * @param   null|string $alias
     * @param   null|string $value
     * @param   null|string $data_type
     *
     * @return  $this
     * @since   1.0
     */
    public function select($column_name, $alias = null, $value = null, $data_type = null)
    {
        $this->editRequired('column_name', $column_name);

        if ($data_type === 'special') {
            $column = $column_name;
        } else {
            $column = $this->setColumnName($column_name);
        }

        $item         = new stdClass();
        $item->column = $column;

        if ($alias === null || trim($alias) == '') {
            $item->alias = null;
        } else {
            $item->alias = $this->quoteName($alias);
        }

        $item = $this->selectDataType($item, $data_type, $value);

        $item = $this->select($item, $data_type);

        $this->columns[$column_name] = $item;

        return $this;
    }

    /**
     * Select Data Type
     *
     * @param   string $data_type
     * @param   object $item
     *
     * @return  $this
     * @since   1.0
     */
    protected function selectDataType($item, $data_type, $value)
    {
        if ($data_type === null || trim($data_type) == '') {
            $item->data_type = null;
            $item->value     = null;

        } elseif ($data_type === 'special') {
            $item->data_type = $data_type;
            $item->value     = $value;

        } else {
            $item->data_type = $data_type;
            $item->value     = $this->filter($item->column, $value, $item->data_type);
        }

        return $item;
    }

    /**
     * Set From table name and optional value for alias
     *
     * @param   string      $table_name
     * @param   null|string $alias
     *
     * @return  $this
     * @since   1.0
     */
    public function from($table_name, $alias = null)
    {
        $this->editRequired('table_name', $table_name);

        $table = $this->quoteName($table_name);

        if ($alias === null || trim($alias) == '') {
        } else {
            $table .= ' AS ' . $this->quoteName($alias);
        }

        $this->from[] = $table;

        return $this;
    }

    /**
     * Create a grouping for conditions for 'and' or 'or' treatment between groups of conditions
     *
     * @param   string $group
     * @param   string $group_connector
     *
     * @return  $this
     * @since   1.0
     */
    public function whereGroup($group, $group_connector = 'AND')
    {
        $this->editRequired('where group name', $group);

        $group_connector = $this->editConnector($group_connector);

        $this->where_group[$group] = $group_connector;

        return $this;
    }

    /**
     * Set Where Conditions for Query
     *
     * @param   string      $left_filter
     * @param   string      $left
     * @param   string      $condition
     * @param   string      $right_filter
     * @param   string      $right
     * @param   string      $connector
     * @param   null|string $group
     *
     * @return  $this
     * @since   1.0
     */
    public function where(
        $left_filter = 'column',
        $left = '',
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = null
    ) {
        $this->editWhere($left_filter, $condition, $right);

        if ($group === null) {
            $group = '';
        }

        $connector = $this->editConnector($connector);

        $left  = $this->setOrFilterColumn($left_filter, $left);

        if (strtolower($condition) == 'in') {
            $right = $this->processInArray('Right', $right, $right_filter);
        } else {
            $right = $this->setOrFilterColumn($right_filter, $right);
        }

        $item            = new stdClass();
        $item->left      = $left;
        $item->condition = $condition;
        $item->right     = $right;
        $item->connector = $connector;
        $item->group     = $group;
        $this->where[]   = $item;

        return $this;
    }

    /**
     * Group By column name and optional value for alias
     *
     * @param   string      $column_name
     * @param   null|string $alias
     *
     * @return  $this
     * @since   1.0
     */
    public function groupBy($column_name, $alias = null)
    {
        $this->editRequired('group by column_name', $column_name);

        $column = $this->setColumnName($column_name);

        if ($alias === null || trim($alias) == '') {
        } else {
            $column = $this->quoteName($alias) . '.' . $column;
        }

        $this->group_by[] = $column;

        return $this;
    }

    /**
     * Create a grouping for having statements for 'and' or 'or' treatment between groups of conditions
     *
     * @param   string $group
     * @param   string $group_connector
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function havingGroup($group, $group_connector = 'AND')
    {
        $this->editRequired('group by group name', $group);

        $group_connector = $this->editConnector($group_connector);

        $this->having_group[$group] = $group_connector;

        return $this;
    }

    /**
     * Set Having Conditions for Query
     *
     * @param   string      $left_filter
     * @param   string      $left
     * @param   string      $condition
     * @param   string      $right_filter
     * @param   string      $right
     * @param   string      $connector
     * @param   string|null $group
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function having(
        $left_filter = 'column',
        $left = '',
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = null)
    {
        $this->editWhere($left_filter, $condition, $right);

        if ($group === null) {
            $group = '';
        }

        $connector = $this->editConnector($connector);

        $left  = $this->setOrFilterColumn($left_filter, $left);
        $right = $this->setOrFilterColumn($right_filter, $right);

        $item            = new stdClass();
        $item->left      = $left;
        $item->condition = $condition;
        $item->right     = $right;
        $item->connector = $connector;
        $item->group     = $group;
        $this->having[]  = $item;

        return $this;
    }

    /**
     * Order By column name and optional value for alias
     *
     * @param   string      $column_name
     * @param   null|string $direction
     *
     * @return  $this
     * @since   1.0
     */
    public function orderBy($column_name, $direction = 'ASC')
    {
        $this->editRequired('order by column_name', $column_name);

        $column = $this->setColumnName($column_name);

        $direction = strtoupper(trim($direction));
        if ($direction == 'DESC') {
            $column = $column . ' ' . 'DESC';
        } else {
            $column = $column . ' ' . 'ASC';
        }

        $this->order_by[] = $column;

        return $this;
    }

    /**
     * Offset and Limit
     *
     * @param   int $offset
     * @param   int $limit
     *
     * @return  $this
     * @since   1.0
     */
    public function setOffsetAndLimit($offset = 0, $limit = 0)
    {
        if ((int)$offset > 0) {
        } else {
            $offset = 0;
        }

        if ((int)$limit > 0) {
        } elseif ((int)$offset > 0) {
            $limit = 15;
        }

        $this->limit = $limit;

        $this->offset = $offset;

        return $this;
    }

    /**
     * Handle Column Name
     *
     * @param   string $column_name
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setColumnName($column_name)
    {
        if (strpos($column_name, '.')) {

            $temp = explode('.', $column_name);

            if (count($temp) == 2) {
                $prefix = $this->quoteName($temp[0]);

                if (trim($temp[1]) == '*') {
                    $column = $prefix . '.*';
                } else {
                    $column = $prefix . '.' . $this->quoteName($temp[1]);
                }

            } else {
                throw new RuntimeException(
                    'Query-setColumnName Method: Illegal Value for $column_name: ' . $column_name
                );
            }

        } else {
            if (trim($column_name) == '*') {
                $column = '*';
            } else {
                $column = $this->quoteName($column_name);
            }
        }

        return $column;
    }

    /**
     * Process Array of Values for IN condition
     *
     * @param       string $filter
     * @param string       $name
     * @param string       $value_string
     */
    protected function processInArray($name, $value_string, $filter)
    {
        if (is_array($value_string) && count($value_string) > 0) {
            $temp         = implode(',', $value_string);
            $value_string = $temp;
        }

        $filtered_array = array();

        $temp = explode(',', $value_string);

        foreach ($temp as $value) {
            $filtered_array[] = $this->filter($name, trim($value), $filter);
        }

        return $filtered_array;
    }
}
