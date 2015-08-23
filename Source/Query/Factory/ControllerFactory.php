<?php
/**
 * Controller Factory
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Factory;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Query\ModelInterface;
use CommonApi\Query\QueryInterface;
use CommonApi\Resource\FactoryInterface;

/**
 * Controller Factory
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ControllerFactory implements FactoryInterface
{
    /**
     * Query Instance
     *
     * @var    object  CommonApi\Query\QueryInterface
     * @since  1.0
     */
    protected $query = null;

    /**
     * Model Instance
     *
     * @var    object  CommonApi\Query\ModelInterface
     * @since  1.0
     */
    protected $model;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $model_registry = null;

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data = null;

    /**
     * Schedule Event - anonymous function to FrontController schedule_event method
     *
     * @var    callable
     * @since  1.0
     */
    protected $schedule_event;

    /**
     * Get Cache - anonymous function to FrontController getCache method
     *
     * @var    callable
     * @since  1.0
     */
    protected $get_cache_callback;

    /**
     * Set Cache - anonymous function to FrontController setCache method
     *
     * @var    callable
     * @since  1.0
     */
    protected $set_cache_callback;

    /**
     * Function to Delete Cache, either by key or all
     *
     * @var    callable
     * @since  1.0
     */
    protected $delete_cache_callback;

    /**
     * SQL
     *
     * @var    string
     * @since  1.0
     */
    protected $sql;

    /**
     * Crud Type
     *
     * @var    string
     * @since  1.0
     */
    public $crud_type = null;

    /**
     * Constructor
     *
     * @param  QueryInterface $query
     * @param  ModelInterface $model
     * @param  array          $model_registry
     * @param  object         $runtime_data
     * @param  callback       $schedule_event
     * @param  callback       $get_cache_callback
     * @param  callback       $set_cache_callback
     * @param  callable       $delete_cache_callback
     * @param  string         $sql
     * @param  string         $crud_type
     *
     * @since  1.0
     */
    public function __construct(
        QueryInterface $query,
        ModelInterface $model,
        $model_registry,
        $runtime_data,
        $schedule_event = null,
        $get_cache_callback = null,
        $set_cache_callback = null,
        $delete_cache_callback = null,
        $sql = '',
        $crud_type = 'read'
    ) {
        $this->query                 = $query;
        $this->model                 = $model;
        $this->model_registry        = $model_registry;
        $this->runtime_data          = $runtime_data;
        $this->schedule_event        = $schedule_event;
        $this->get_cache_callback    = $get_cache_callback;
        $this->set_cache_callback    = $set_cache_callback;
        $this->delete_cache_callback = $delete_cache_callback;
        $this->sql                   = $sql;
        $this->crud_type             = $crud_type;
    }

    /**
     * Instantiate Class, load the Model Registry and SQL
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $class = 'Molajo\\Controller\\' . ucfirst(strtolower($this->crud_type));

        try {
            $instance = new $class (
                $this->model,
                $this->runtime_data,
                $this->query,
                $this->schedule_event,
                $this->get_cache_callback,
                $this->set_cache_callback,
                $this->delete_cache_callback
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                'Molajo\\Query\\Factory\\ControllerFactory::instantiateClass Failed Instantiating: '
                . $class
                . ' '
                . $e->getMessage()
            );
        }

        $instance->clearQuery();

        $instance->setModelRegistry(null, $this->model_registry);

        if ($this->crud_type === 'Create') {
            $this->crud_type = 'insert';
        } elseif ($this->crud_type === 'Createfrom') {
            $this->crud_type = 'insertfrom';
        } else {
            $this->crud_type = strtolower($this->crud_type);
        }

        $instance->setType($this->crud_type);

        if (trim($this->sql) === '') {
        } else {
            $instance->set('sql', $this->sql);
        }

        return $instance;
    }
}
