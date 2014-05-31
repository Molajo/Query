<?php
/**
 * Model Registry Criteria Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

/**
 * Model Registry Criteria Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Criteria extends Columns
{
    /**
     * I. KEY CRITERIA
     *
     * Set Where Statements for ID or Name Keys
     *
     * @return  $this
     * @since   1.0
     */
    protected function setKeyCriteria()
    {
        $primary_key = $this->model_registry['primary_key_value'];
        $name_key    = $this->model_registry['name_key_value'];

        if ((int)$primary_key > 0) {
            return $this->setWhereStatementsKeyValue('primary_key', 'integer', $primary_key);
        }

        if (trim($name_key) === '') {
            return $this;
        }

        $this->setWhereStatementsKeyValue('name_key', 'string', $name_key);

        return $this;
    }

    /**
     * Set Where Statements: Key Provided
     *
     * @param   string $key
     * @param   string $filter
     * @param   string $key_value
     *
     * @return  $this
     * @since   1.0
     */
    protected function setWhereStatementsKeyValue($key, $filter, $key_value)
    {
        $this->setWhere(
            'column',
            $this->model_registry['primary_prefix'] . '.' . $this->model_registry[$key],
            '=',
            $filter,
            $key_value
        );

        return $this;
    }

    /**
     * IV. JOINS
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
     *      <joins>
     *          <join model="Catalogtypes"
     *              etc=stuff />
     *      </joins>
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useJoins()
    {
        if ($this->model_registry['use_special_joins'] === 0) {
            return false;
        }

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
            $this->setFrom($join_table, $alias);
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
            $this->setSelect(
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
     * Process each Join Item Piar
     *
     * @param   string $join_to
     * @param   string $join_with
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setJoinItemWhereLoop(
        $join_to_array,
        $join_to_item_alias,
        $join_with_array,
        $join_with_item_alias,
        $operator)
    {
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

    /**
     * V. MODEL REGISTRY CRITERIA
     *
     * These are either defined within the <model statement or set in the class executing the query
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelCriteria()
    {
        $this->setModelCriteriaWhere('status', 'IN', 'integer', 'criteria_status');
        $this->setModelCriteriaWhere('catalog_type_id', '=', 'integer', 'criteria_catalog_type_id');
        $this->setModelCriteriaWhere('extension_instance_id', '=', 'integer', 'criteria_extension_instance_id');
        $this->setModelCriteriaWhere('menu_id', '=', 'integer', 'criteria_menu_id');

        return $this;
    }

    /**
     * Model Registry Criteria: Builds Where Clause
     *
     * @param   string $column_name
     * @param   string $operator
     * @param   string $filter
     * @param   string $model_registry_property
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelCriteriaWhere(
        $column_name,
        $operator,
        $filter,
        $model_registry_property
    ) {

        if ($this->useModelCriteriaWhere($column_name) === false) {
            return $this;
        }

        $this->setWhere(
            'column',
            $this->model_registry['primary_prefix'] . '.' . $column_name,
            $operator,
            $filter,
            $this->model_registry[$model_registry_property]
        );

        return $this;
    }

    /**
     * Should this Model Criteria be used?
     *
     * @param   string $column_name
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useModelCriteriaWhere($column_name)
    {
        if (isset($this->model_registry[$column_name])) {
        } else {
            return false;
        }

        if ($this->model_registry[$column_name] === ''
            || (int)$this->model_registry[$column_name] === 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * VI. MODEL REGISTRY CRITERIA ARRAY
     *
     *      <criteria>
     *          <where name="a.enabled"
     *              connector="="
     *              value="1"/>
     *          <where name="a.redirect_to_id"
     *              connector="="
     *              value="0"/>
     *      </criteria>
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelCriteriaArrayCriteria()
    {
        if ($this->useModelCriteriaArray() === false) {
            return $this;
        }

        foreach ($this->model_registry['criteria'] as $item) {
            $this->setModelRegistryCriteriaArrayItem($item);
        }

        return $this;
    }

    /**
     * Model Registry Criteria Array: Use or Don't Use
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function useModelCriteriaArray()
    {
        if (count($this->model_registry['criteria']) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Model Registry Criteria Array: Use or Don't Use
     *
     * @param   array $item
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelRegistryCriteriaArrayItem($item)
    {
        if (isset($item['value'])) {
            $this->setWhere('column', $item['name'], $item['connector'], 'integer', (int)$item['value']);

        } elseif (isset($item['name2'])) {
            $this->setWhere('column', $item['name'], $item['connector'], 'column', $item['name2']);
        }

        return $this;
    }

    /**
     * COMMON CODE
     *
     * Set "Where Pair" Left - Operator - Right
     *
     * @param   string $join_to_item_alias
     * @param   string $join_to_item
     * @param   string $operator
     * @param   string $join_with_item_alias
     * @param   string $join_with_item
     *
     * @return  $this
     * @since   1.0
     */
    protected function setWherePair(
        $join_to_item_alias,
        $join_to_item,
        $operator,
        $join_with_item_alias,
        $join_with_item
    ) {
        /** Left */
        list($join_to_filter, $join_to_value) = $this->setWhereElement($join_to_item_alias, $join_to_item);

        /** Operator */
        list($operator) = $this->setWhereOperator($operator);

        /** Right */
        list($join_with_filter, $join_with_value) = $this->setWhereElement($join_with_item_alias, $join_with_item);

        /** Set the Where Statement */
        $this->setWhere($join_to_filter, $join_to_value, $operator, $join_with_filter, $join_with_value);

        return $this;
    }

    /**
     * Set Where Operator
     *
     * @param   string $operator
     *
     * @return  string
     * @since   1.0
     */
    protected function setWhereOperator($operator)
    {
        if (in_array($operator, $this->operator_array)) {
            return $operator;
        }

        $operator = '=';

        return $operator;
    }

    /**
     * Add Model Registry Criteria to Query
     *
     * @param   string $join_with_item_alias
     * @param   string $join_item
     *
     * @return  array
     * @since   1.0
     */
    protected function setWhereElement($join_with_item_alias, $join_item)
    {
        if (isset($this->query_where_property_array[$join_item])) {
            return $this->setWhereElementProperty($join_item);
        }

        if (is_numeric($join_item)) {
            return $this->setWhereElementNumericValue($join_item);
        }

        return $this->setWhereElementTableColumn($join_with_item_alias, $join_item);
    }

    /**
     * Where element is a named property (ex. APPLICATION_ID)
     *
     * @param   string $join_item
     *
     * @return  array
     * @since   1.0
     */
    protected function setWhereElementProperty($join_item)
    {
        $key = $this->query_where_property_array[$join_item];

        if (isset($this->model_registry[$key])) {
            $value = $this->model_registry[$key];
        } else {
            $value = $this->$key;
        }

        return array('integer', $value);
    }

    /**
     * Where element is a numeric value
     *
     * @param   string $join_item
     *
     * @return  string[]
     * @since   1.0
     */
    protected function setWhereElementNumericValue($join_item)
    {
        return array('integer', $join_item);
    }

    /**
     * Where element is a table column
     *
     * @param   string $join_with_item_alias
     * @param   string $join_item
     *
     * @return  string[]
     * @since   1.0
     */
    protected function setWhereElementTableColumn($join_with_item_alias, $join_item)
    {
        return array('column', $join_with_item_alias . '.' . $join_item);
    }
}
