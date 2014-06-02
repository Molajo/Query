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
     * Model Instance  CommonApi\Model\ModelInterface
     *
     * @var    object
     * @since  1.0
     */
    protected $model;

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
     * List of Controller Properties
     *
     * @var    array
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
            'current_date'
        );

    /**
     * Class Constructor
     *
     * @param  ModelInterface $model
     * @param  array          $runtime_data
     * @param  array          $plugin_data
     * @param  callable       $schedule_event
     *
     * @since  1.0
     */
    public function __construct(
        ModelInterface $model = null,
        $runtime_data = array(),
        $plugin_data = array(),
        callable $schedule_event = null
    ) {
        $this->model          = $model;
        $this->runtime_data   = $runtime_data;
        $this->plugin_data    = $plugin_data;
        $this->schedule_event = $schedule_event;

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
        if (isset($this->$key)) {
        } else {
            return false;
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
     */
    public function setValue($key, $value = null)
    {
        if (in_array($key, $this->property_array)) {
            $this->$key = $value;
        }

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
