<?php
/**
 * Abstract Controller Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Controller\ControllerInterface;
use CommonApi\Model\ModelInterface;
use CommonApi\Query\QueryInterface;

/**
 * Abstract Controller Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Controller implements ControllerInterface
{
    /**
     * Query Instance  CommonApi\Query\QueryInterface
     *
     * @var    object
     * @since  1.0
     */
    protected $query;

    /**
     * Model Instance  CommonApi\Model\ModelInterface
     *
     * @var    object
     * @since  1.0
     */
    protected $model;

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = null;

    /**
     * Stores an array of key/value runtime_data settings
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data = null;

    /**
     * Stores an array of key/value plugin data settings
     *
     * @var    object
     * @since  1.0
     */
    protected $plugin_data = null;

    /**
     * Schedule Event Callback
     *
     * @var    callable
     * @since  1.0
     */
    protected $schedule_event;

    /**
     * Raw SQL string
     *
     * @var    string
     * @since  1.0
     */
    protected $sql = '';

    /**
     * Site ID
     *
     * @var    int
     * @since  1.0
     */
    protected $site_id = null;

    /**
     * Application ID
     *
     * @var    int
     * @since  1.0
     */
    protected $application_id = null;

    /**
     * Set of rows returned from a query
     *
     * @var    array
     * @since  1.0
     */
    protected $query_results = array();

    /**
     * Single set of $query_results and used in create, update, delete operations
     *
     * @var    object
     * @since  1.0
     */
    protected $row;

    /**
     * Used in queries to determine date validity
     *
     * @var    string
     * @since  1.0
     */
    protected $null_date;

    /**
     * Today's CCYY-MM-DD 00:00:00 formatted for query
     *
     * @var    string
     * @since  1.0
     */
    protected $current_date;

    /**
     * List of Controller Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array
        = array(
            'query',
            'model',
            'runtime_data',
            'plugin_data',
            'schedule_event',
            'sql',
            'site_id',
            'application',
            'query_results',
            'row',
            'null_date',
            'current_date',
            'cache'
        );

    /**
     * Class Constructor
     *
     * @param  QueryInterface $query
     * @param  ModelInterface $model
     * @param  object         $runtime_data
     * @param  object         $plugin_data
     * @param  callable       $schedule_event
     * @param  array          $model_registry
     *
     * @since  1.0
     */
    public function __construct(
        QueryInterface $query,
        ModelInterface $model,
        $runtime_data,
        $plugin_data,
        callable $schedule_event,
        array $model_registry
    ) {
        $this->query          = $query;
        $this->model          = $model;
        $this->runtime_data   = $runtime_data;
        $this->plugin_data    = $plugin_data;
        $this->schedule_event = $schedule_event;

        $this->setModelRegistryDefaults($model_registry);
        $this->setSiteApplicationProperties();
    }

    /**
     * Get the current value (or default) of the specified property
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function getValue($key, $default = null)
    {
        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set the value of the specified property
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  $this
     * @since   1.0
     */
    public function setValue($key, $value = null)
    {
        if (in_array($key, $this->property_array)) {
            $this->$key = $value;
        }

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
     * Set Default Values for SQL
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSiteApplicationProperties()
    {
        if (isset($this->runtime_data->application->id)) {
            $this->application_id = $this->runtime_data->application->id;
        }

        if (isset($this->runtime_data->site->id)) {
            $this->site_id = $this->runtime_data->site->id;
        }

//todo: FIX
        $this->site_id        = 2;
        $this->application_id = 2;

        return $this;
    }
}
