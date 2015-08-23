<?php
/**
 * Base Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Query\ControllerInterface;
use CommonApi\Query\ModelInterface;

/**
 * Base Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Base implements ControllerInterface
{
    /**
     * Model Instance
     *
     * @var    object  CommonApi\Query\ModelInterface
     * @since  1.0
     */
    protected $model;

    /**
     * Stores an array of key/value runtime_data settings
     *
     * @var    array
     * @since  1.0
     */
    protected $runtime_data = null;

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
     *
     * @since  1.0
     */
    public function __construct(
        ModelInterface $model = null,
        $runtime_data = array()
    ) {
        $this->model        = $model;
        $this->runtime_data = $runtime_data;

        $this->setSiteApplicationProperties();
    }

    /**
     * Get the current value (or default) of the specified property
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function getValue($key, $default = null)
    {
        if (isset($this->$key)) {
        } else {
            return null;
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
     * @since   1.0.0
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
     * @since   1.0.0
     */
    protected function setSiteApplicationProperties()
    {
        if (isset($this->runtime_data->application->id)) {
            $this->application_id = $this->runtime_data->application->id;
        }

        if (isset($this->runtime_data->site->id)) {
            $this->site_id = $this->runtime_data->site->id;
        }

        return $this;
    }
}
