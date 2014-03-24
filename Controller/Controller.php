<?php
/**
 * Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Controller\ControllerInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Model\ModelInterface;

/**
 * Primary controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
abstract class Controller implements ControllerInterface
{
    /**
     * Model Instance  CommonApi\Query\ModelInterface
     *
     * @var    object
     * @since  1.0
     */
    public $model;

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
     * List of Controller Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'model',
        'runtime_data',
        'plugin_data',
        'schedule_event',
        'sql',
        'query_results',
        'row'
    );

    /**
     * Class Constructor
     *
     * @param ModelInterface $model
     * @param object         $runtime_data
     * @param object         $plugin_data
     * @param callable       $schedule_event
     * @param string         $sql
     *
     * @since   1.0
     */
    public function __construct(
        ModelInterface $model,
        $runtime_data,
        $plugin_data,
        callable $schedule_event,
        $sql = ''
    ) {
        $this->model         = $model;
        $this->runtime_data  = $runtime_data;
        $this->plugin_data   = $plugin_data;
        $this->scheduleEvent = $schedule_event;
        $this->sql           = $sql;

        if ($this->model->getModelRegistry('query_object')) {
            $query_object = $this->model->getModelRegistry('query_object');
        } else {
            $query_object = '';
        }

        if (in_array($query_object, array('result', 'item', 'list', 'distinct'))) {
        } else {
            $this->model->setModelRegistry('query_object', 'list');
        }
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
            throw new RuntimeException('Controller Get: Unknown key: ' . $key);
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
            throw new RuntimeException('Controller Set: Unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this;
    }

    /**
     * Get a specified Model key value
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function getModel($key, $default = null)
    {
        if ($this->model->$key === null) {
            $this->model->$key = $default;
        }

        return $this->model->$key;
    }

    /**
     * Set a specified Model key value
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setModel($key, $value = null)
    {
        $this->model->$key = $value;

        return $this;
    }

    /**
     * Get the current value (or default) of the Model Registry
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getModelRegistry($key, $default = null)
    {
        return $this->model->getModelRegistry($key, $default);
    }

    /**
     * Set the value of the specified Model Registry
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setModelRegistry($key, $value = null)
    {
        $this->model->setModelRegistry($key, $value);

        return $this;
    }
}
