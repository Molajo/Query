<?php
/**
 * Model Registry Base
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\ModelRegistry;

use CommonApi\Controller\ModelRegistryInterface;
use CommonApi\Query\QueryInterface;

/**
 * Model Registry Base
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Base implements ModelRegistryInterface
{
    /**
     * Query Instance  CommonApi\Query\QueryInterface
     *
     * @var    object
     * @since  1.0
     */
    protected $query;

    /**
     * Model Registry
     *
     * @var    array
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
     * @param  QueryInterface $query_object
     * @param  array          $model_registry
     *
     * @since  1.0
     */
    public function __construct(
        QueryInterface $query_object,
        array $model_registry = array()
    ) {
        $this->query_object   = $query_object;
        $this->model_registry = $model_registry;
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
}
