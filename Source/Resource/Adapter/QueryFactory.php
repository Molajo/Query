<?php
/**
 * Query Factory
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Adapter;

use CommonApi\Exception\RuntimeException;
use Exception;

/**
 * Query Factory
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class QueryFactory extends NamespaceHandler
{
    /**
     * Database Instance
     *
     * @var    object   CommonApi\Query\DatabaseInterface
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
     * Schedule Event - anonymous function to FrontController schedule_event method
     *
     * @var    callable
     * @since  1.0
     */
    protected $schedule_event;

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
    protected $runtime_data;

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
     * @param  string $base_path
     * @param  array  $resource_map
     * @param  array  $namespace_prefixes
     * @param  array  $valid_file_extensions
     * @param  array  $cache_callbacks
     * @param  array  $handler_options
     *
     * @since  1.0
     */
    public function __construct(
        $base_path = null,
        array $resource_map = array(),
        array $namespace_prefixes = array(),
        array $valid_file_extensions = array(),
        array $cache_callbacks = array(),
        array $handler_options = array()
    ) {
        parent::__construct(
            $base_path,
            $resource_map,
            $namespace_prefixes,
            $valid_file_extensions,
            $cache_callbacks
        );

        $this->setClassProperties($handler_options);
    }

    /**
     * Set Class Properties for Handler Options Array
     *
     * @param   array $handler_options
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setClassProperties(array $handler_options = array())
    {
        $this->database       = $handler_options['database'];
        $this->query          = $handler_options['query'];
        $this->schedule_event = $handler_options['schedule_event'];

        return $this;
    }

    /**
     * Create Model Instance
     *
     * @param   string $crud_type
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function createModel($crud_type)
    {
        $class = 'Molajo\\Query\\Factory\\ModelFactory';

        try {
            return new $class (
                clone $this->database,
                $crud_type
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                'Resource QueryFactory Failed Instantiating Controller: '
                . $e->getMessage()
            );
        }
    }

    /**
     * Create Controller Instance
     *
     * @param   string $crud_type
     * @param   object $model
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function createController($crud_type, $model)
    {
        $class = 'Molajo\\Query\\Factory\\ControllerFactory';

        try {
            return new $class (
                clone $this->query,
                $model,
                $this->model_registry,
                $this->runtime_data,
                $this->schedule_event,
                $this->get_cache_callback,
                $this->set_cache_callback,
                $this->delete_cache_callback,
                $this->sql,
                $crud_type
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                'Resource QueryFactory Handler Failed Instantiating Controller: '
                . $e->getMessage()
            );
        }
    }
}
