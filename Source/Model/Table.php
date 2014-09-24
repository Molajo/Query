<?php
/**
 * Model Registry Table
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

/**
 * Model Registry Table
 *
 * Base - Query - Utilities - Defaults - Table - Columns - Criteria - Registry
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Table extends Defaults
{
    /**
     * Set From Table Value
     *
     * @return  $this
     * @since   1.0
     */
    protected function setFrom()
    {
        if ($this->useFromTable() === false) {
            return $this;
        }

        $primary_prefix = $this->model_registry['primary_prefix'];
        $table_name     = $this->model_registry['table_name'];

        $this->from($table_name, $primary_prefix);

        return $this;
    }

    /**
     * Determine whether or not to use the From Table Value
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useFromTable()
    {
        if (isset($this->model_registry['table_name'])) {
        } else {
            return false;
        }

        if ($this->model_registry['table_name'] === '') {
            return false;
        }

        return true;
    }

    /**
     * JOINS
     *
     * Special Joins defined in model registry to build SQL statements
     *
     *      <joins>
     *          <join model="Catalogtypes"
     *              etc=stuff />
     *      </joins>
     *
     * @return  $this
     * @since   1.0
     */
    protected function setJoins()
    {
        if ($this->useJoins() === false) {
            return $this;
        }

        $joins = $this->model_registry['joins'];

        foreach ($joins as $join) {
            $this->setJoinItem($join);
        }

        return $this;
    }

    /**
     * Use Joins?
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useJoins()
    {
//        if ($this->model_registry['use_special_joins'] === 0) {
//            return false;
//        }

        if (count($this->model_registry['joins']) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Process a single "join" item
     *
     *      <join model="Catalogtypes"
     *          alias="b"
     *          select="title,model_type,model_name,primary_category_id,alias"
     *          jointo="id"
     *          joinwith="catalog_type_id"/>
     *
     * @param   string $join
     *
     * @return  $this
     * @since   1.0
     */
    protected function setJoinItem($join)
    {
        $join_table = $join['table_name'];
        $alias      = $join['alias'];
        $select     = $join['select'];
        $join_to    = $join['jointo'];
        $join_with  = $join['joinwith'];

        $column_results = $this->setJoinItemColumns($select, $alias);

        $where_results = $this->setJoinItemWhere($join_to, $join_with, $alias);

        if ($column_results === false && $where_results === false) {
        } else {
            $this->from($join_table, $alias);
        }

        return $this;
    }

    /**
     * Process the select columns for a single join item
     *
     *          alias="b"
     *          select="title,model_type,model_name,primary_category_id"
     *
     * @param   string $select
     * @param   string $alias
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setJoinItemColumns($select, $alias)
    {
        if ($this->useJoinItemColumns($select) === false) {
            return false;
        }

        $select_array = explode(',', $select);

        foreach ($select_array as $select_item) {
            $this->select(
                trim($alias) . '.' . trim($select_item),
                trim($alias) . '_' . trim($select_item)
            );
        }

        return false;
    }

    /**
     * Use Join Item Columns in Select Statement?
     *
     * @param   string $select
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useJoinItemColumns($select = '')
    {
        if (trim($select) === '') {
            return false;
        }

        if ($this->model_registry['query_object'] === 'result'
            || $this->model_registry['query_object'] === 'distinct'
        ) {
            return false;
        }

        return true;
    }

    /**
     * Process each of the "jointo" and "joinwith" pairs (there can be multiple)
     *
     *          alias="b"
     *          jointo="id"
     *          joinwith="catalog_type_id"/>
     *
     * @param   string $join_to
     * @param   string $join_with
     * @param   string $alias
     *
     * @return  $this
     * @since   1.0
     */
    protected function setJoinItemWhere($join_to, $join_with, $alias)
    {
        if ($this->useJoinItemWhere($join_to, $join_with) === false) {
            return $this;
        }

        $this->setJoinItemWhereLoop(
            explode(',', $join_to),
            $alias,
            explode(',', $join_with),
            $this->model_registry['primary_prefix'],
            '='
        );

        return $this;
    }

    /**
     * Process each Join Item Pair
     *
     * @param   array  $join_to_array
     * @param   array  $join_with_array
     * @param   string $join_to_item_alias
     * @param   string $operator
     *
     * @return  boolean|null
     * @since   1.0
     */
    protected function setJoinItemWhereLoop(
        $join_to_array,
        $join_to_item_alias,
        $join_with_array,
        $join_with_item_alias,
        $operator
    ) {
        $i = 0;
        foreach ($join_to_array as $join_to_item) {

            $join_with_item = $join_with_array[$i];

            $this->setWherePair(
                $join_to_item_alias,
                $join_to_item,
                $operator,
                $join_with_item_alias,
                $join_with_item
            );

            $i++;
        }
    }

    /**
     * Use Join Item Where Statements?
     *
     * @param   string $join_to
     * @param   string $join_with
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useJoinItemWhere($join_to, $join_with)
    {
        $join_to_array   = explode(',', $join_to);
        $join_with_array = explode(',', $join_with);

        if (count($join_to_array) === 0) {
            return false;
        }

        if (count($join_to_array) === count($join_with_array)) {
            return true;
        }

        return false;
    }
}
