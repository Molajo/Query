<?php
/**
 * Abstract Controller Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Cache\CacheInterface;
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
     * Cache
     *
     * @var    object  CommonApi\Cache\CacheInterface
     * @since  1.0
     */
    protected $cache;

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
            'model_registry',
            'runtime_data',
            'plugin_data',
            'schedule_event',
            'sql',
            'site',
            'application',
            'null_date',
            'current_date',
            'cache',
            'query_results',
            'row'
        );

    /**
     * Class Constructor
     *
     * @param  QueryInterface $query
     * @param  ModelInterface $model
     * @param  array          $model_registry
     * @param  object         $runtime_data
     * @param  object         $plugin_data
     * @param  callable       $schedule_event
     * @param  string         $sql
     * @param  integer       $site_id
     * @param  integer       $application_id
     * @param  string         $null_date
     * @param  string         $current_date
     * @param  CacheInterface $cache
     *
     * @since  1.0
     */
    public function __construct(
        QueryInterface $query,
        ModelInterface $model,
        array $model_registry,
        $runtime_data,
        $plugin_data,
        callable $schedule_event,
        $sql,
        $null_date,
        $current_date,
        CacheInterface $cache = null,
        $site_id = 0,
        $application_id = 0
    ) {
        $this->runtime_data   = $runtime_data;
        $this->plugin_data    = $plugin_data;
        $this->schedule_event = $schedule_event;
        $this->cache          = $cache;

        $this->setDateProperties($null_date, $current_date);
        $this->setModelProperties($query, $model, $sql);
        $this->setSiteApplicationProperties($site_id, $application_id);
        $this->setModelRegistryDefaults($model_registry);
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
     * Set Default Values for SQL
     *
     * @param   QueryInterface $query,
     * @param   ModelInterface $model,
     * @param   string         $sql
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelProperties(QueryInterface $query, ModelInterface $model, $sql)
    {
        $this->query          = $query;
        $this->model          = $model;

        $this->sql = $sql;

        if ($this->sql === null || trim($this->sql) == '') {
            $this->sql = '';
        }

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
        $defaults = new ModelRegistry($model_registry);

        $this->model_registry = $defaults->setModelRegistryDefaults();

        return $this;
    }

    /**
     * Set Default Values for SQL
     *
     * @param   string  $null_date
     * @param   string  $current_date
     *
     * @return  $this
     * @since   1.0
     */
    protected function setDateProperties($null_date, $current_date)
    {
        $this->null_date    = $null_date;
        $this->current_date = $current_date;

        return $this;
    }

    /**
     * Set Default Values for SQL
     *
     * @param   integer  $site_id
     * @param   integer  $application_id
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSiteApplicationProperties($site_id, $application_id)
    {
        $this->site_id        = (int)$site_id;
        $this->application_id = (int)$application_id;

//todo: FIX
        $this->site_id        = 2;
        $this->application_id = 2;

        return $this;
    }
}
