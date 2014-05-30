<?php
/**
 * Abstract Collect Query Elements
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Adapter;

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
        if (in_array(strtolower($query_type), $this->query_type_array)) {
            $this->query_type = strtolower($query_type);
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

        $column = $this->selectColumn($data_type, $column_name);

        $item         = new stdClass();
        $item->column = $column;

        if ($alias === null || trim($alias) === '') {
            $item->alias = null;
        } else {
            $item->alias = $this->quoteName($alias);
        }

        $item = $this->selectDataType($item, $data_type, $value);

        $this->columns[$column_name] = $item;

        return $this;
    }

    /**
     * Select Column
     *
     * @param   string $data_type
     * @param   string $column_name
     *
     * @return  string
     * @since   1.0
     */
    protected function selectColumn($data_type, $column_name)
    {
        if ($data_type === 'special') {
            $column = $column_name;
        } else {
            $column = $this->setColumnName($column_name);
        }

        return $column;
    }

    /**
     * Select Data Type
     *
     * @param   string    $data_type
     * @param   stdClass  $item
     * @param null|string $value
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function selectDataType($item, $data_type, $value)
    {
        if ($data_type === null || trim($data_type) === '') {
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

        if ($alias === null || trim($alias) === '') {
        } else {
            $table .= ' AS ' . $this->quoteName($alias);
        }

        $this->from[] = $table;

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
        return $this->setHavingWhereGroup($group, $group_connector, 'having');
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
        return $this->setHavingWhereGroup($group, $group_connector, 'where');
    }

    /**
     * Create a grouping for having statements for 'and' or 'or' treatment between groups of conditions
     *
     * @param   string $group
     * @param   string $group_connector
     * @param   string $type
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setHavingWhereGroup($group, $group_connector = 'AND', $type = 'where')
    {
        $this->editRequired('setHavingWhereGroup', $group);

        $group_connector = $this->editConnector($group_connector);

        $property = $type . '_group';
        $this->$property[$group] = $group_connector;

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
        $group = ''
    ) {
        $this->having = $this->setWhereHaving(
            $left_filter,
            $left,
            $condition,
            $right_filter,
            $right,
            $connector,
            $group,
            $this->having
        );

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
     * @param   string|null $group
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function where(
        $left_filter = 'column',
        $left = '',
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = ''
    ) {
        $this->where = $this->setWhereHaving(
            $left_filter,
            $left,
            $condition,
            $right_filter,
            $right,
            $connector,
            $group,
            $this->where
        );

        return $this;
    }

    /**
     * Set Where Conditions for Query (also used for having)
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
    public function setWhereHaving(
        $left_filter = 'column',
        $left = '',
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = '',
        array $type_array = array()
    ) {
        $this->editWhere($left, $condition, $right);

        $connector     = $this->editConnector($connector);
        $left          = $this->setWhereHavingLeft($left, $left_filter);
        $right         = $this->setWhereHavingRight($right, $right_filter, $connector);
        $type_array[]  = $this->buildItem($left, $condition, $right, $connector, $group);

        return $type_array;
    }

    /**
     * Set Where Conditions for Left
     *
     * @param   mixed $value
     * @param   string $filter
     *
     * @return  null|string
     * @since   1.0
     */
    public function setWhereHavingLeft(
        $value,
        $filter = 'column'
    ) {
        return $this->setOrFilterColumn('leftwhere', $value, $filter);
    }

    /**
     * Set Where Conditions for Right
     *
     * @param   mixed  $value
     * @param   string $filter
     * @param   string $condition
     *
     * @return  null|string
     * @since   1.0
     */
    public function setWhereHavingRight(
        $value,
        $filter = 'column',
        $condition = '='
    ) {
        if (strtolower($condition) === 'in') {
            $temp = $this->processInArray('rightwhere', $value, $filter);
            return explode(',', $temp);
        }

        return $this->setOrFilterColumn('rightwhere', $value, $filter);
    }

    /**
     * Build Item for SQL Portions
     *
     * @param   mixed       $left
     * @param   string      $condition
     * @param   mixed       $right
     * @param   string      $connector
     * @param   null|string $group
     *
     * @return  stdClass
     * @since   1.0
     */
    public function buildItem(
        $left = '',
        $condition = '=',
        $right = '',
        $connector = 'AND',
        $group = null
    ) {
        $item            = new stdClass();
        $item->left      = $left;
        $item->condition = $condition;
        $item->right     = $right;
        $item->connector = $connector;
        $item->group     = $group;

        return $item;
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
        return $this->setGroupByOrderBy($column_name, $alias, null, 'group_by');
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
        if ($direction === 'DESC') {
            $column = $column . ' ' . 'DESC';
        } else {
            $column = $column . ' ' . 'ASC';
        }

        $this->order_by[] = $column;

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
    public function setGroupByOrderBy($column_name, $alias, $direction = 'ASC', $type)
    {


        $this->editRequired('group by column_name', $column_name);

        $column = $this->setColumnName($column_name);

        $this->group_by[] = $column;

        return $this;

        $this->editRequired('setGroupByOrderBy column_name', $column_name);

        $column = $this->setColumnName($column_name);


        return $this;
    }

    /**
     * Finish Group By
     *
     * @param   string $column
     *
     * @return  $this
     * @since   1.0
     */
    public function setGroupByOrderByFinishGroupBy($column)
    {
        $this->group_by[] = $column;

        return $this;
    }

    /**
     * Finish Order By
     *
     * @param   string $column
     * @param   string $direction
     *
     * @return  $this
     * @since   1.0
     */
    public function setGroupByOrderByFinishOrderBy($column, $direction)
    {
        $direction = strtoupper(trim($direction));

        if ($direction === 'DESC') {
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
        $this->setOffsetorLimit($offset, $type = 'offset');
        $this->setOffsetorLimit($limit, $type = 'limit');

        return $this;
    }

    /**
     * Set Offset or Limit
     *
     * @param   int $offset
     * @param   int $limit
     *
     * @return  $this
     * @since   1.0
     */
    public function setOffsetorLimit($value, $type = 'offset')
    {
        if ((int)$value > 0) {
        } else {
            $value = 0;
        }

        if ($type === 'limit') {
            if ((int)$value === 0) {
                $value = 15;
            }
        }

        $this->$type = $value;

        return $this;
    }

    /**
     * Handle Column Name
     *
     * @param   string $column_name
     *
     * @return  string
     * @since   1.0
     */
    protected function setColumnName($column_name)
    {
        if (strpos($column_name, '.')) {
            $temp   = explode('.', $column_name);
            $column = $this->quoteNameAndAlias($temp[1], $temp[0]);
        } else {
            $column = $this->quoteName($column_name);
        }

        return $column;
    }

    /**
     * Process Array of Values for IN condition
     *
     * @param string $name
     * @param string $value_string
     * @param string $filter
     *
     * @return  string
     * @since   1.0
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
