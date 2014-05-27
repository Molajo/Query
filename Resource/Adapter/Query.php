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
use CommonApi\Database\DatabaseInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Query\QueryInterface;
use CommonApi\Resource\AdapterInterface;
use stdClass;

/**
 * Query Resource Handler - Instantiates Model and Controller
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Query extends Xml implements AdapterInterface
{
    /**
     * Database Instance
     *
     * @var    object   CommonApi\Database\DatabaseInterface
     * @since  1.0
     */
    protected $database;

    /**
     * Query Object
     *
     * @var    object   CommonApi\Query\QueryInterface
     * @since  1.0
     */
    protected $query = null;

    /**
     * Model Registry - data source/object fields and definitions
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
     * @param  string            $base_path
     * @param  array             $resource_map
     * @param  array             $namespace_prefixes
     * @param  array             $valid_file_extensions
     * @param  DatabaseInterface $database
     * @param  QueryInterface    $query
     * @param  callback          $schedule_event
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
        $this->schedule_event = $schedule_event;
        $this->runtime_data   = new stdClass();
        $this->plugin_data    = new stdClass();
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
            throw new RuntimeException(
                'Resource XmlHandler handlePath options array must have namespace entry.'
            );
        }

        $segments = explode('//', $options['namespace']);
        if (count($segments) > 2) {
        } else {
            throw new RuntimeException(
                'Resource XmlHandler Failure namespace must have at least 3 segments:  '
            . $options['namespace']
            );
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

        try {
            return new $class (
                $this->database
            );
        } catch (Exception $e) {
            throw new RuntimeException('Resource Query Handler Failed Instantiating Controller: '
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
                $this->query,
                $model,
                $this->model_registry,
                $this->runtime_data,
                $this->plugin_data,
                $this->schedule_event,
                $this->sql

            );
        } catch (Exception $e) {
            throw new RuntimeException('Resource Query Handler Failed Instantiating Controller: '
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
