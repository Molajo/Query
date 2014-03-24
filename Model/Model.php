<?php
/**
 * Abstract Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Model;

use CommonApi\Authorisation\AuthorisationInterface;
use CommonApi\Cache\CacheInterface;
use CommonApi\Database\DatabaseInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Model\FieldhandlerInterface;
use CommonApi\Model\ModelInterface;

/**
 * Abstract Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
abstract class Model implements ModelInterface
{
    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    public $model_registry = null;

    /**
     * Database Instance
     *
     * @var    object   CommonApi\Database\DatabaseInterface
     * @since  1.0
     */
    public $database = null;

    /**
     * Query Object
     *
     * @var    object   CommonApi\Database\QueryObjectInterface
     * @since  1.0
     */
    public $query = null;

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
     * Filter Fields Instance
     *
     * @var    object  CommonApi\Model\FieldhandlerInterface
     * @since  1.0
     */
    protected $fieldhandler;

    /**
     * Authorisation Instance
     *
     * @var    object  CommonApi\Authorisation\AuthorisationInterface
     * @since  1.0
     */
    protected $authorisation = null;

    /**
     * Cache
     *
     * @var    object  CommonApi\Cache\CacheInterface
     * @since  1.0
     */
    protected $cache;

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
     * Single Row from Query Results
     *
     * @var    string
     * @since  1.0
     */
    protected $row = null;

    /**
     * Results from queries
     *
     * @var    array
     * @since  1.0
     */
    protected $query_results = array();

    /**
     * List of Properties
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
        'model_registry',
        'database',
        'query',
        'null_date',
        'current_date',
        'fieldhandler',
        'authorisation',
        'cache',
        'site_id',
        'application_id',
        'row',
        'query_results'
    );

    /**
     * Constructor
     *
     * @param  string                 $model_registry
     * @param  DatabaseInterface      $database
     * @param  object                 $query
     * @param  string                 $null_date
     * @param  string                 $current_date
     * @param  FieldhandlerInterface  $fieldhandler
     * @param  AuthorisationInterface $authorisation
     * @param  CacheInterface         $cache
     * @param  null|int               $site_id
     * @param  null|int               $application_id
     *
     * @since  1.0
     */
    public function __construct(
        $model_registry,
        DatabaseInterface $database,
        $query,
        $null_date,
        $current_date,
        FieldhandlerInterface $fieldhandler,
        AuthorisationInterface $authorisation,
        CacheInterface $cache = null,
        $site_id = 0,
        $application_id = 0
    ) {
        $this->model_registry = $model_registry;
        $this->database       = $database;
        $this->query          = $query;
        $this->null_date      = $null_date;
        $this->current_date   = $current_date;
        $this->fieldhandler   = $fieldhandler;
        $this->authorisation  = $authorisation;
        $this->cache          = $cache;
        $this->site_id        = (int)$site_id;
        $this->application_id = (int)$application_id;
    }

    /**
     * Get the current value (or default) of the specified property
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function get($key, $default = null)
    {
        if (in_array($key, $this->property_array)) {
        } else {
            throw new RuntimeException('Model Get: Unknown key: ' . $key);
        }

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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function set($key, $value = null)
    {
        if (in_array($key, $this->property_array)) {
        } else {
            throw new RuntimeException('Model Set: Unknown key: ' . $key);
        }

        $this->$key = $value;

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
     * Return Query String
     *
     * @return  string
     * @since   1.0
     */
    public function getQueryString()
    {
        return $this->database->getQueryString();
    }
}
