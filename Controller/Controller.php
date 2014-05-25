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
use CommonApi\Exception\RuntimeException;
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
     * @param  null|int       $site_id
     * @param  null|int       $application_id
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
        $this->query          = $query;
        $this->model          = $model;
        $this->model_registry = $model_registry;
        $this->runtime_data   = $runtime_data;
        $this->plugin_data    = $plugin_data;
        $this->schedule_event = $schedule_event;
        $this->sql            = $sql;

        if ($this->sql === null || trim($this->sql) == '') {
            $this->sql = '';
        }
        $this->null_date      = $null_date;
        $this->current_date   = $current_date;
        $this->cache          = $cache;
        $this->site_id        = (int)$site_id;
        $this->application_id = (int)$application_id;

//todo: FIX
        $this->site_id        = 2;
        $this->application_id = 2;

        $this->setModelRegistryDefaults();
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
            return $this->model_registry;
        }

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
    protected function setModelRegistryDefaults()
    {
        if (is_array($this->model_registry)) {
        } else {
            $this->model_registry = array();
        }

        $this->setModelRegistryDefaultsGroup();
        $this->setModelRegistryCriteriaValues();
        $this->setModelRegistryDefaultsKeys();
        $this->setModelRegistryDefaultsFields();
        $this->setModelRegistryDefaultsTableName();
        $this->setModelRegistryDefaultsPrimaryPrefix();
        $this->setModelRegistryDefaultsQueryObject();
        $this->setModelRegistryDefaultsCriteriaArray();
        $this->setModelRegistryDefaultsJoins();
        $this->setModelRegistryDefaultLimits();

        return $this;
    }

    /**
     * Set Field Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsGroup()
    {
        if (isset($this->model_registry['process_events'])) {
            $process_events = $this->model_registry['process_events'];
        } else {
            $process_events = 1;
        }

        if ($process_events === 0) {
        } else {
            $process_events = 1;
        }

        $this->model_registry['process_events'] = $process_events;

        return $this;
    }

    /**
     * Set Primary Key Defaults for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryCriteriaValues()
    {
        $this->setProperty('criteria_status', '');
        $this->setProperty('criteria_source_id', 0);
        $this->setProperty('criteria_catalog_type_id', 0);
        $this->setProperty('catalog_type_id', 0);
        $this->setProperty('menu_id', 0);
        $this->setProperty('criteria_extension_instance_id', 0);

        return $this;
    }

    /**
     * Set Primary Key Defaults for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsKeys()
    {
        $this->setProperty('primary_key', 'id');
        $this->setProperty('primary_key_value', 0);

        $key = 0;

        if (isset($this->model_registry['primary_key_value'])) {
            $key = (int)$this->model_registry['primary_key_value'];
        }

        if ((int)$this->model_registry['primary_key_value'] === 0) {
            if (isset($this->model_registry['criteria_source_id'])) {
                $key = (int)$this->model_registry['criteria_source_id'];
            }
        }

        if ($key === 0) {
            $this->model_registry['primary_key_value'] = null;
        } else {
            $this->model_registry['primary_key_value'] = $key;
        }

        $this->setProperty('name_key', 'title');
        $this->setProperty('name_key_value, null');

        return $this;
    }

    /**
     * Set Field Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsFields()
    {
        return $this->setPropertyArray('fields', array());
    }

    /**
     * Set Table Name Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsTableName()
    {
        return $this->setProperty('table_name', '#__content');
    }

    /**
     * Set Primary Prefix Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsPrimaryPrefix()
    {
        return $this->setProperty('primary_prefix', 'a');
    }

    /**
     * Set Query Object Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsQueryObject()
    {
        $this->setProperty('query_object', 'list');

        $query_object = strtolower($this->model_registry['query_object']);

        if ($query_object == 'result'
            || $query_object == 'item'
            || $query_object == 'list'
            || $query_object == 'distinct'
        ) {
        } else {
            $query_object = 'list';
        }

        $this->model_registry['query_object'] = $query_object;

        return $this;
    }

    /**
     * Set Criteria Array Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsCriteriaArray()
    {
        return $this->setPropertyArray('criteria', array());
    }

    /**
     * Set Joins Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsJoins()
    {
        $this->setProperty('use_special_joins', 0);
        $this->setPropertyArray('joins', array());

        return $this;
    }

    /**
     * Set Offset, Count and Pagination Defaults for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultLimits()
    {
        $this->setProperty('model_offset', 0);
        $this->setProperty('model_count', 15);
        $this->setProperty('use_pagination', 1);

        $model_offset   = $this->model_registry['model_offset'];
        $model_count    = $this->model_registry['model_count'];

        if (isset($this->model_registry['use_pagination'])) {
            $use_pagination = $this->model_registry['use_pagination'];
        } else {
            $use_pagination = 0;
            $model_offset   = 0;
            $model_count    = 0;
        }

        if ($model_offset == 0 && $model_count == 0) {

            if ($this->model_registry['query_object'] == 'result') {
                $model_offset   = 0;
                $model_count    = 0;
                $use_pagination = 0;

            } elseif ($this->model_registry['query_object'] == 'distinct') {
                $model_offset   = 0;
                $model_count    = 0;
                $use_pagination = 0;

            } else {
                $model_offset   = 0;
                $model_count    = 15;
                $use_pagination = 1;
            }
        }

        if ($this->model_registry['query_object'] == 'list') {
        } else {
            $model_offset   = 0;
            $model_count    = 0;
            $use_pagination = 0;
        }

        if ($use_pagination === 0) {
            $model_offset = 0;
            $model_count  = 99999999;
        }

        $this->model_registry['use_pagination'] = $use_pagination;
        $this->model_registry['model_offset']   = $model_offset;
        $this->model_registry['model_count']    = $model_count;

        return $this;
    }

    /**
     * Set Property
     *
     * @param   string $property
     * @param   mixed $default
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPropertyArray($property, $default = array())
    {
        $this->setProperty($property, $default);

        if (is_array($this->model_registry[$property])) {
        } else {
            $this->model_registry[$property] = array();
        }

        return $this;
    }

    /**
     * Set Property
     *
     * @param   string $property
     * @param   mixed $default
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setProperty($property, $default = null)
    {
        if (isset($this->model_registry[$property])) {
            $value = trim($this->model_registry[$property]);
        } else {
            $value = $default;
        }

        $this->model_registry[$property] = $value;

        return $this;
    }
}
