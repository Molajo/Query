<?php
/**
 * Query Resource Handler
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Adapter;

use Exception;
use CommonApi\Authorisation\AuthorisationInterface;
use CommonApi\Cache\CacheInterface;
use CommonApi\Database\DatabaseInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Model\FieldhandlerInterface;
use CommonApi\Resource\AdapterInterface;
use stdClass;

/**
 * Query Resource Handler - Instantiates Model and Controller
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0
 */
class Query extends Xml implements AdapterInterface
{
    /**
     * Database Instance
     *
     * @var    object
     * @since  1.0
     */
    protected $database;

    /**
     * Query Object
     *
     * @var    object
     * @since  1.0
     */
    protected $query = null;

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
     * AuthorisationInterface Instance
     *
     * @var    object  CommonApi\Authorisation\AuthorisationInterface
     * @since  1.0
     */
    protected $authorisation;

    /**
     * Fieldhandler Instance (Validation, Filtering, Formatting/Escaping)
     *
     * @var    object
     * @since  1.0
     */
    protected $fieldhandler;

    /**
     * Cache
     *
     * @var    object  CommonApi\Cache\CacheInterface
     * @since  1.0
     */
    protected $cache;

    /**
     * Model Registry - data source/object fields and definitions
     * type, name, model_registry_name, query_object
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry;

    /**
     * Query runtime_data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data = null;

    /**
     * Plugin data
     *
     * @var    object
     * @since  1.0
     */
    protected $plugin_data = null;

    /**
     * Schedule Event - anonymous function to FrontController schedule_event method
     *
     * @var    callable
     * @since  1.0
     */
    protected $schedule_event;

    /**
     * SQL
     *
     * @var    string
     * @since  1.0
     */
    protected $sql;

    /**
     * Constructor
     *
     * @param  string                  $base_path
     * @param  array                   $resource_map
     * @param  array                   $namespace_prefixes
     * @param  array                   $valid_file_extensions
     * @param  DatabaseInterface       $database
     * @param                          $query
     * @param  string                  $null_date
     * @param  string                  $current_date
     * @param  AuthorisationInterface  $authorisation
     * @param  FieldhandlerInterface   $fieldhandler
     * @param  CacheInterface          $cache
     * @param  callback                $schedule_event
     *
     * @since  1.0
     */
    public function __construct(
        $base_path = null,
        array $resource_map = array(),
        array $namespace_prefixes = array(),
        array $valid_file_extensions = array(),
        DatabaseInterface $database,
        $query,
        $null_date,
        $current_date,
        AuthorisationInterface $authorisation,
        FieldhandlerInterface $fieldhandler,
        CacheInterface $cache = null,
        callable $schedule_event
    ) {
        parent::__construct(
            $base_path,
            $resource_map,
            $namespace_prefixes,
            $valid_file_extensions
        );

        $this->database       = $database;
        $this->query          = $query;
        $this->null_date      = $null_date;
        $this->current_date   = $current_date;
        $this->authorisation  = $authorisation;
        $this->fieldhandler   = $fieldhandler;
        $this->cache          = $cache;
        $this->runtime_data   = new stdClass();
        $this->plugin_data    = new stdClass();
        $this->schedule_event = $schedule_event;
    }

    /**
     * Handle requires located file
     *
     * @param   string $scheme
     * @param   string $located_path
     * @param   array  $options
     *
     * @return  void|mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function handlePath($scheme, $located_path, array $options = array())
    {
        if (isset($options['namespace'])) {
        } else {
            throw new RuntimeException
            ('Resource XmlHandler handlePath options array must have namespace entry.');
        }

        $segments = explode('//', $options['namespace']);
        if (count($segments) > 2) {
        } else {
            throw new RuntimeException
            ('Resource XmlHandler Failure namespace must have at least 3 segments:  ' . $options['namespace']);
        }

        $this->model_registry = $options['xml'];

        if (isset($options['sql'])) {
            $this->sql = $options['sql'];
        }

        if (isset($options['runtime_data'])) {
            $this->runtime_data = $options['runtime_data'];
        }

        if (isset($options['plugin_data'])) {
            $this->plugin_data = $options['plugin_data'];
        }

        if (isset($this->model_registry['model_offset'])) {
        } else {
            $this->model_registry['model_offset'] = 0;
        }

        if (isset($this->model_registry['model_count'])) {
        } else {
            $this->model_registry['model_count'] = 20;
        }

        if (isset($this->model_registry['use_pagination'])) {
        } else {
            $this->model_registry['use_pagination'] = 1;
        }

        $type = 'read';
        if (isset($options['crud_type'])) {
            $type = $options['crud_type'];
        }

        $type = ucfirst(strtolower($type));
        if ($type === 'Create'
            || $type === 'Read'
            || $type === 'Update'
            || $type === 'Delete'
        ) {
        } else {
            $type = 'Read';
        }

        $model = $this->createModel($type)->instantiateClass();

        return $this->createController($type, $model)->instantiateClass();
    }

    /**
     * Create Model Instance
     *
     * @param   string $type
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function createModel($type)
    {
        $class = 'Molajo\\Resource\\Factory\\' . $type . 'ModelFactory';

        $application_id = 0;
        if (isset($this->runtime_data->application->id)) {
            $application_id = $this->runtime_data->application->id;
        }
//todo SITEID

        $site_id = 2;

        try {
            return new $class (
                $this->model_registry,
                $this->database,
                $this->null_date,
                $this->current_date,
                $this->fieldhandler,
                $this->authorisation,
                $this->cache,
                $site_id,
                $application_id
            );
        } catch (Exception $e) {
            throw new RuntimeException ('Resource Query Handler Failed Instantiating Controller: '
            . $e->getMessage());
        }
    }

    /**
     * Create Controller Instance
     *
     * @param   string $type
     * @param   object $model
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function createController($type, $model)
    {
        $class = 'Molajo\\Resource\\Factory\\' . $type . 'ControllerFactory';

        try {
            return new $class (
                $model,
                $this->runtime_data,
                $this->plugin_data,
                $this->schedule_event,
                $this->sql
            );
        } catch (Exception $e) {
            throw new RuntimeException ('Resource Query Handler Failed Instantiating Controller: '
            . $e->getMessage());
        }
    }

    /**
     * Retrieve a collection of a specific handler
     *
     * @param   string $scheme
     * @param   array  $options
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getCollection($scheme, array $options = array())
    {
        return null;
    }
}
