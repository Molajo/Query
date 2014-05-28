<?php
/**
 * Model Registry Query Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

/**
 * Model Registry Query
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ModelRegistryQuery extends ModelRegistryQueryCriteria
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
     * Total
     *
     * @var    integer
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
        $this->setFromTable();
        $this->setWhereStatements();
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

        $this->setSelectColumnsModelRegistry();

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
            $this->select($this->model_registry['primary_prefix'] . '.' . '*');
        } else {
            foreach ($this->model_registry['fields'] as $column) {
                $this->select($this->model_registry['primary_prefix'] . '.' . $column['name']);
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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setFromTable()
    {
        if (count($this->get('from', array())) > 0) {
            return $this;
        }

        $primary_prefix = $this->model_registry['primary_prefix'];
        $table_name     = $this->model_registry['table_name'];

        $this->from($table_name, $primary_prefix);

        return $this;
    }
}
