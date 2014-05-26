<?php
/**
 * Read Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Exception\RuntimeException;

/**
 * Read Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ModelRegistryQueryController extends QueryController
{
    /**
     * Query Object
     *
     * List, Item, Result, Distinct
     *
     * @var    string
     * @since  1.0
     */
    protected $query_object;

    /**
     * Use Pagination
     *
     * @var    integer
     * @since  1.0
     */
    protected $use_pagination;

    /**
     * Offset
     *
     * @var    integer
     * @since  1.0
     */
    protected $offset;

    /**
     * Count
     *
     * @var    integer
     * @since  1.0
     */
    protected $count;

    /**
     * Offset Count
     *
     * @var    integer
     * @since  1.0
     */
    protected $offset_count;

    /**
     * Total
     *
     * @var    integer
     * @since  1.0
     */
    protected $total;

    /**
     * DRIVER: SELECT, FROM and WHERE clauses for Query based on Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistrySQL()
    {
        if (count($this->get('columns', array())) === 0) {
            $this->setSelectColumns();
        }

        if (count($this->get('from', array())) === 0) {
            $this->setFromTable();
        }

        if (count($this->get('where', array())) === 0) {
            $this->setWhereStatements();
        }

        $this->setSpecialJoins();
        $this->setModelRegistryCriteria();
        $this->setModelRegistryCriteriaArrayCriteria();

        $this->query_object   = $this->getModelRegistry('query_object');
        $this->use_pagination = $this->getModelRegistry('use_pagination');
        $this->offset         = $this->getModelRegistry('offset');
        $this->count          = $this->getModelRegistry('count');

        return $this;
    }

    /**
     * I. COLUMNS
     *
     * Create "select columns" statements when no columns have been specified
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSelectColumns()
    {
        if ($this->model_registry['query_object'] == 'result') {
            return $this->setSelectColumnsResultQuery();
        }

        if ($this->model_registry['query_object'] == 'distinct') {
            $this->setSelectColumnsDistinctQuery();
        }

        if (count($this->model_registry['columns']) === 0) {
            $this->select($this->model_registry['primary_prefix'] . '.' . '*');
        } else {
            $this->setSelectColumnsModelRegistry();
        }

        return $this;
    }

    /**
     * Create "select columns" statements: Result Query
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSelectColumnsResultQuery()
    {
        if ((int)$this->model_registry['id'] > 0) {
            $this->select($this->model_registry['primary_prefix'] . '.' . $this->model_registry['name_key']);
            return $this;
        }

        $this->select($this->model_registry['primary_prefix'] . '.' . $this->model_registry['primary_key']);

        return $this;
    }

    /**
     * Create "select columns" statements: Distinct Query
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSelectColumnsDistinctQuery()
    {
        $this->setDistinct(true);
    }

    /**
     * Create "select columns" statements: Model Registry Columns
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSelectColumnsModelRegistry()
    {
        foreach ($this->model_registry['columns'] as $column) {
            $this->select($this->model_registry['primary_prefix'] . '.' . $column['name']);
        }
    }

    /**
     * II. FROM TABLE
     *
     * Set From Table Value
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setFromTable()
    {
        $primary_prefix = $this->model_registry['primary_prefix'];
        $table_name     = $this->model_registry['table_name'];

        $this->from($table_name, $primary_prefix);

        return $this;
    }

    /**
     * III. SET FROM TABLE
     *
     * Set Where Statements
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setWhereStatements()
    {
        if ((int)$this->model_registry['id'] > 0) {
            $this->setWhereStatementsIDSpecified();

        } elseif (trim($this->model_registry['name_key_value']) == '') {

        } else {
            $this->setWhereStatementsNameKeySpecified();
        }

        return $this;
    }

    /**
     * Set Where Statements: ID Key Provided
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setWhereStatementsIDSpecified()
    {
        $this->where(
            'column',
            $this->model_registry['primary_prefix'] . '.' . $this->model_registry['primary_key'],
            '=',
            'integer',
            $this->model_registry['primary_key_value']
        );
    }

    /**
     * Set Where Statements: Name Key Specified
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setWhereStatementsNameKeySpecified()
    {
        $this->where(
            'column',
            $this->model_registry['primary_prefix'] . '.' . $this->model_registry['name_key'],
            '=',
            'string',
            $this->model_registry['name_key_value']
        );
    }


    /**
     * IV. SPECIAL JOINS
     *
     * Special Joins defined in model registry to build SQL statements
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setSpecialJoins()
    {
        if ($this->model_registry['use_special_joins'] === 0
            || count($this->model_registry['joins']) === 0
        ) {
            return $this;
        }

        $joins = $this->model_registry['joins'];

        foreach ($joins as $join) {
            $this->setSpecialJoinsItem($join);
        }

        return $this;
    }

    /**
     * Special Joins: Process Single Item Join
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setSpecialJoinsItem($join)
    {
        $join_table = $join['table_name'];
        $alias      = $join['alias'];
        $select     = $join['select'];
        $join_to    = $join['jointo'];
        $join_with  = $join['joinwith'];

        $this->setSpecialJoinsItemColumns($select, $alias);

        $join_to_array     = explode(',', $join_to);
        $join_with_array   = explode(',', $join_with);
        $where_left        = null;
        $where_left_alias  = $alias;
        $where_right       = null;
        $where_right_alias = $this->model_registry['primary_prefix'];


        if (count($join_to_array) > 0) {

            $i = 0;
            foreach ($join_to_array as $join_to_item) {

                $this->setSpecialJoinsItemWhere(
                    $join_to_item,
                    $where_left_alias,
                    $join_with_array[$i],
                    $where_right_alias,
                    $join_table,
                    $alias
                );

                $i++;
            }
        }

        return $this;
    }

    /**
     * Special Joins: Single Item Columns
     *
     * @param   string $select
     * @param   string $alias
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSpecialJoinsItemColumns($select, $alias)
    {
        if (trim($select) == '') {
            $select_array = array();
        } else {
            $select_array = explode(',', $select);
        }

        if ($this->model_registry['query_object'] == 'result') {
        } else {

            if (count($select_array) > 0) {
                foreach ($select_array as $select_item) {
                    $this->select(
                        trim($alias) . '.' . trim($select_item),
                        trim($alias) . '_' . trim($select_item)
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Special Joins: Where - Left - criteria - Right
     *
     * @param   string $join_to_item
     * @param   string $where_left_alias
     * @param   string $with
     * @param   string $where_right_alias
     * @param   string $join_table
     * @param   string $alias
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSpecialJoinsItemWhere(
        $join_to_item,
        $where_left_alias,
        $with,
        $where_right_alias,
        $join_table,
        $alias
    ) {
        /** Where Left */
        list($where_left_filter, $where_left) = $this->setSpecialJoinsItemWhereLeft(
            $join_to_item,
            $where_left_alias
        );

        /** Operator */
        list($with, $operator) = $this->setSpecialJoinsItemWhereOperator($with);

        /** Right */
        list($where_right_filter, $where_right) = $this->setSpecialJoinsItemWhereRight($where_right_alias, $with);

        /** Where Left Operator Right */
        $this->setSpecialJoinsItemWhereLeftOperatorRight(
            $join_table,
            $alias,
            $where_left,
            $where_right,
            $where_left_filter,
            $operator,
            $where_right_filter
        );

        return $this;
    }

    /**
     * Set Special Joins Item - "Where Left"
     *
     * @param   string $join_to_item
     * @param   string $where_left_alias
     *
     * @return  array
     * @since   1.0
     */
    protected function setSpecialJoinsItemWhereLeft($join_to_item, $where_left_alias)
    {
        $results = $this->setWhereElement($join_to_item, $where_left_alias);

        $where_left_filter = $results[0];
        $where_left        = $results[1];

        return array($results, $where_left_filter, $where_left);
    }

    /**
     * Set Special Joins Item - "Operator"
     *
     * @param   string $with
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSpecialJoinsItemWhereOperator($with)
    {
        $operator = '=';
        if (substr($with, 0, 2) == '>=') {
            $operator = '>=';
            $with     = substr($with, 2, strlen($with) - 2);
            return array($with, $operator);

        } elseif (substr($with, 0, 1) == '>') {
            $operator = '>';
            $with     = substr($with, 0, strlen($with) - 1);
            return array($with, $operator);

        } elseif (substr($with, 0, 2) == '<=') {
            $operator = '<=';
            $with     = substr($with, 2, strlen($with) - 2);
            return array($with, $operator);

        } elseif (substr($with, 0, 1) == '<') {
            $operator = '<';
            $with     = substr($with, 0, strlen($with) - 1);
            return array($with, $operator);
        }
        return array($with, $operator);
    }

    /**
     * Set Special Joins Item - "Right"
     *
     * @param   string $where_right_alias
     * @param   string $with
     *
     * @return  array
     * @since   1.0
     */
    protected function setSpecialJoinsItemWhereRight($where_right_alias, $with)
    {
        $results = $this->setWhereElement($with, $where_right_alias);

        $where_right_filter = $results[0];
        $where_right        = $results[1];

        return array($where_right_filter, $where_right);
    }

    /**
     * Set Special Joins Item - "Where Left Operator Right"
     *
     * @param   string $join_table
     * @param   string $alias
     * @param   string $where_left
     * @param   string $where_right
     * @param   string $where_left_filter
     * @param   string $operator
     * @param   string $where_right_filter
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSpecialJoinsItemWhereLeftOperatorRight(
        $join_table,
        $alias,
        $where_left,
        $where_right,
        $where_left_filter,
        $operator,
        $where_right_filter
    ) {
        if ($where_left === null || $where_right === null) {

        } else {

            if ($join_table === null) {
            } else {
                $this->from($join_table, $alias);
                $join_table = null;
            }

            $this->where(
                $where_left_filter,
                $where_left,
                $operator,
                $where_right_filter,
                $where_right
            );
        }
    }

    /**
     * Add Model Registry Criteria to Query
     *
     * @param   string $join_item
     *
     * @return  array
     * @since   1.0
     */
    protected function setWhereElement($join_item, $alias)
    {
        $filter     = 'column';
        $where_part = null;

        if ($join_item == 'APPLICATION_ID') {
            if ((int)$this->application_id === 0) {

            } else {
                $where_part = $this->application_id;
                $filter     = 'integer';

                return array($filter, $where_part);
            }
        }

        if ($join_item == 'SITE_ID') {
            if ((int)$this->site_id === 0) {
            } else {
                $where_part = $this->site_id;
                $filter     = 'integer';
            }

            return array($filter, $where_part);
        }

        if ($join_item == 'MENU_ID') {
            $where_part = (int)$this->model_registry['criteria_menu_id'];
            $filter     = 'integer';

            return array($filter, $where_part);
        }

        if ($join_item == 'CATALOG_TYPE_ID') {
            $where_part = (int)$this->model_registry['catalog_type_id'];
            $filter     = 'integer';

            return array($filter, $where_part);
        }

        if (is_numeric($join_item)) {
            $where_part = (int)$join_item;
            $filter     = 'integer';

            return array($filter, $where_part);
        }

        $has_alias = explode('.', $join_item);

        if (count($has_alias) > 1) {
            $to_join = trim($has_alias[0]) . '.' . trim($has_alias[1]);
        } else {
            $to_join = trim($alias) . '.' . trim($join_item);
        }

        $where_part = $to_join;


        return array($filter, $where_part);
    }

    /**
     * V. MODEL REGISTRY CRITERIA
     *
     * Uses joins defined in model registry to build SQL statements
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModelRegistryCriteria()
    {
        if ($this->model_registry['criteria_status'] === '') {
        } else {
            $this->setModelRegistryCriteriaBuildWhere('status', 'IN', 'integer', 'criteria_status');
        }

        if ((int)$this->model_registry['criteria_catalog_type_id'] === 0) {
        } else {
            $this->setModelRegistryCriteriaBuildWhere('catalog_type_id', '=', 'integer', 'criteria_catalog_type_id');
        }

        if ((int)$this->model_registry['criteria_extension_instance_id'] === 0) {
        } else {
            $this->setModelRegistryCriteriaBuildWhere(
                'extension_instance_id',
                '=',
                'integer',
                'criteria_extension_instance_id'
            );
        }

        if ((int)$this->model_registry['criteria_menu_id'] === 0) {
        } else {
            $this->setModelRegistryCriteriaBuildWhere('menu_id', '=', 'integer', 'criteria_menu_id');
        }

        return $this;
    }

    /**
     * Model Registry Criteria: Builds Where Clause
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModelRegistryCriteriaBuildWhere(
        $column_name,
        $operator,
        $filter,
        $model_registry_property
    ) {
        $this->where(
            'column',
            $this->model_registry['primary_prefix'] . '.' . $column_name,
            $operator,
            $filter,
            $this->model_registry[$model_registry_property]
        );

        return $this;
    }

    /**
     * VI. MODEL REGISTRY CRITERIA ARRAY
     *
     * Set Criteria Statements
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModelRegistryCriteriaArrayCriteria()
    {
        if (count($this->model_registry['criteria']) > 0) {
        } else {
            return $this;
        }

        foreach ($this->model_registry['criteria'] as $item) {

            if ($this->SetModelRegistryCriteriaArrayUse($item) === true) {

                if (isset($item['value'])) {
                    $this->where('column', $item['name'], $item['connector'], 'integer', (int)$item['value']);

                } elseif (isset($item['name2'])) {
                    $this->where('column', $item['name'], $item['connector'], 'column', $item['name2']);
                }
            }
        }

        return $this;
    }

    /**
     * Model Registry Criteria Array: Use or Don't Use
     *
     * @param   array $item
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function SetModelRegistryCriteriaArrayUse($item)
    {
        if ($this->SetModelRegistryCriteriaArrayUseModelRegistry() === true) {
            return $this;
        }

        return $this->SetModelRegistryCriteriaArrayUseItem($item);
    }

    /**
     * Model Registry Criteria Array: Use or Don't Use "use special joins" in Registry
     *
     * @param   array $item
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function SetModelRegistryCriteriaArrayUseModelRegistry()
    {
        if ($this->model_registry['use_special_joins'] === 0
            || count($this->model_registry['joins']) === 0
        ) {
            $use_special_joins = false;
        } else {
            $use_special_joins = true;
        }

        return $use_special_joins;
    }

    /**
     * Model Registry Criteria Array: Use or Don't Use "use special joins" in Registry: Item
     *
     * @param   array $item
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function SetModelRegistryCriteriaArrayUseItem($item)
    {
        if (strpos($item['name'], '.') > 0) {

            $parts = explode('.', $item['name']);

            if ($parts[0] == $this->model_registry['primary_prefix']) {
                $use = true;
                return $use;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Set Model Registry Limits
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModelRegistryLimits()
    {
        if (count($this->model_registry['use_pagination']) == 0) {
        } else {
            return $this;
        }

        $this->setOffsetAndLimit(
            $this->model_registry['offset'],
            $this->model_registry['limit']
        );

        return $this;
    }
}
