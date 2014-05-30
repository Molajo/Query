<?php
/**
 * Model Registry Interface
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Controller\ModelRegistryInterface;
use CommonApi\Model\ModelInterface;
use CommonApi\Query\QueryInterface;

/**
 * Model Registry Interface
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ModelRegistryQuery extends QueryController implements ModelRegistryInterface
{
    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = null;

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
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $query_where_property_array
        = array(
            'APPLICATION_ID'  => 'application_id',
            'SITE_ID'         => 'site_id',
            'MENU_ID'         => 'criteria_menu_id',
            'CATALOG_TYPE_ID' => 'catalog_type_id',
        );

    /**
     * Operator Array
     *
     * @var    array
     * @since  1.0
     */
    protected $operator_array = array('=', '>=', '>', '<=', '<', '<>');

    /**
     * Class Constructor
     *
     * @param  QueryInterface $query
     * @param  ModelInterface $model
     * @param  array          $runtime_data
     * @param  array          $plugin_data
     * @param  callable       $schedule_event
     * @param  array          $model_registry
     *
     * @since  1.0
     */
    public function __construct(
        QueryInterface $query,
        ModelInterface $model = null,
        $runtime_data = array(),
        $plugin_data = array(),
        callable $schedule_event = null,
        array $model_registry = array()
    ) {
        parent::__construct(
            $query,
            $model,
            $runtime_data,
            $plugin_data,
            $schedule_event
        );

        $this->setDateProperties();
        $this->setModelRegistryDefaults($model_registry);
    }

    /**
     * Set Default Values for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaults($model_registry)
    {
        $defaults = new ModelRegistryDefaults($model_registry);

        $this->model_registry = $defaults->setModelRegistryDefaults();

        return $this;
    }

    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function getModelRegistry($key = null, $default = null)
    {
        if ($key == '*' || trim($key) == '' || $key === null) {
            return $this->getModelRegistryAll();
        }

        return $this->getModelRegistryByKey($key, $default);
    }

    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  $this
     * @since   1.0
     */
    public function setModelRegistry($key, $value = null)
    {
        $this->model_registry[$key] = $value;

        return $this;
    }

    /**
     * Get the full contents of the Model Registry
     *
     * @return  mixed
     * @since   1.0
     */
    protected function getModelRegistryAll()
    {
        return $this->model_registry;
    }

    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     */
    protected function getModelRegistryByKey($key = null, $default = null)
    {
        if (isset($this->model_registry[$key])) {
        } else {
            $this->model_registry[$key] = $default;
        }

        return $this->model_registry[$key];
    }

    /**
     * Build SQL from Model Registry
     *
     * @return  string
     * @since   1.0
     */
    public function getSQL($sql = null)
    {
        if ($sql === null) {
            $this->setModelRegistrySQL();
        }

        return parent::getSQL($sql);
    }

    /**
     * Build SQL from Model Registry
     *
     * @return  string
     * @since   1.0
     */
    protected function setModelRegistrySQL()
    {
        $this->setSelectColumns();
        $this->setFromTable();
        $this->setKeyCriteria();
        $this->setJoins();
        $this->setModelCriteria();
        $this->setModelCriteriaArrayCriteria();

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
        if ($this->useSelectColumns() === 0) {
            return $this;
        }

        if ($this->model_registry['query_object'] == 'result') {
            return $this->setSelectColumnsResultQuery();
        }

        if ($this->model_registry['query_object'] == 'distinct') {
            $this->setSelectColumnsDistinctQuery();
        }

        $this->setSelectColumnsModelRegistry();

        return $this;
    }

    /**
     * Should Columns be used for Select List?
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useSelectColumns()
    {
        if (count($this->get('columns', array())) === 0) {
            return false;
        }

        return true;
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
            $this->setSelect($this->model_registry['primary_prefix'] . '.' . $this->model_registry['name_key']);
            return $this;
        }

        $this->setSelect($this->model_registry['primary_prefix'] . '.' . $this->model_registry['primary_key']);

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

        return $this;
    }

    /**
     * Create "select columns" statements: Model Registry Columns
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSelectColumnsModelRegistry()
    {
        if (count($this->model_registry['fields']) === 0) {
            $this->setSelect($this->model_registry['primary_prefix'] . '.' . '*');

        } else {
            foreach ($this->model_registry['fields'] as $column) {
                $this->setSelect($this->model_registry['primary_prefix'] . '.' . $column['name']);
            }
        }

        return $this;
    }

    /**
     * II. FROM TABLE
     *
     * Set From Table Value
     *
     * @return  $this
     * @since   1.0
     */
    protected function setFromTable()
    {
        if (count($this->get('from', array())) > 0) {
            return $this;
        }

        $primary_prefix = $this->model_registry['primary_prefix'];
        $table_name     = $this->model_registry['table_name'];

        $this->setFrom($table_name, $primary_prefix);

        return $this;
    }

    /**
     * III. KEY CRITERIA
     *
     * Set Where Statements for ID or Name Keys
     *
     * @return  $this
     * @since   1.0
     */
    protected function setKeyCriteria()
    {
        if ($this->useKeyCriteria() === false) {
            return $this;
        }

        if ((int)$this->model_registry['primary_key_value'] > 0) {
            $this->setWhereStatementsKeyValue('primary_key', 'integer', 'primary_key_value');

        } elseif (trim($this->model_registry['name_key_value']) == '') {

        } else {
            $this->setWhereStatementsKeyValue('name_key', 'string', 'name_key_value');
        }

        return $this;
    }

    /**
     * Use Key Values to set criteria?
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useKeyCriteria()
    {
        if ((int)$this->model_registry['primary_key_value'] > 0) {
            return true;
        }

        if (trim($this->model_registry['name_key_value']) == '') {
            return false;
        }

        return true;
    }

    /**
     * Set Where Statements: Key Provided
     *
     * @param   string               $key
     * @param   string               $filter
     * @param   mixed|integer|string $key_value
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
            $this->model_registry[$key_value]
        );
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
    protected function UseJoinItemColumns($select)
    {
        if ($this->model_registry['query_object'] === 'result') {
            return false;
        }

        if (trim($select) == '') {
            return false;
        }

        $select_array = explode(',', $select);

        if (count($select_array) === 0) {
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
        $join_to_array   = explode(',', $join_to);
        $join_with_array = explode(',', $join_with);

        if ($this->useJoinItemWhere($join_to_array, $join_with_array) === false) {
            return $this;
        }

        $join_to_item_alias   = $alias;
        $join_with_item_alias = $this->model_registry['primary_prefix'];
        $operator             = '=';

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

        return $this;
    }

    /**
     * Use Join Item Where Statements?
     *
     * @param   array $join_to_array
     * @param   array $join_with_array
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useJoinItemWhere($join_to_array, $join_with_array)
    {
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
     * @return  boolean
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
     * Set Model Registry Limits
     *
     * @return  $this
     * @since   1.0
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
     * @return  $this
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
     * @return  array
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
     * @return  array
     * @since   1.0
     */
    protected function setWhereElementTableColumn($join_with_item_alias, $join_item)
    {
        return array('column', $join_with_item_alias . '.' . $join_item);
    }

    /**
     * INTERACT WITH QUERY CLASS
     *
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
    protected function setSelect($column_name, $alias = null, $value = null, $data_type = null)
    {
        return $this->select($column_name, $alias, $value, $data_type);
    }

    /**
     * Set From
     *
     * @param   string $table_name
     * @param   mixed  $primary_prefix
     *
     * @return  $this
     * @since   1.0
     */
    protected function setFrom($table_name, $primary_prefix)
    {
        $this->from($table_name, $primary_prefix);

        return $this;
    }

    /**
     * Set Where Statement
     *
     * @param   string $join_to_filter
     * @param   mixed  $join_to_value
     * @param   string $operator
     * @param   string $join_with_filter
     * @param   mixed  $join_with_value
     *
     * @return  $this
     * @since   1.0
     */
    protected function setWhere(
        $join_to_filter,
        $join_to_value,
        $operator,
        $join_with_filter,
        $join_with_value
    ) {
        $this->where($join_to_filter, $join_to_value, $operator, $join_with_filter, $join_with_value);

        return $this;
    }
}
