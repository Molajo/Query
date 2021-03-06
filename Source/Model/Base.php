<?php
/**
 * Base Model Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Model;

use CommonApi\Query\DatabaseInterface;
use CommonApi\Query\ModelInterface;

/**
 * Base Model Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Base implements ModelInterface
{
    /**
     * Database Instance
     *
     * @var    object   CommonApi\Query\DatabaseInterface
     * @since  1.0
     */
    public $database = null;

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
    protected $property_array
        = array(
            'database',
            'row',
            'query_results'
        );

    /**
     * Constructor
     *
     * @param  DatabaseInterface $database
     *
     * @since  1.0
     */
    public function __construct(
        DatabaseInterface $database
    ) {
        $this->database = $database;
    }

    /**
     * Get the current value (or default) of the specified property
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function get($key, $default = null)
    {
        $value = $this->$key;

        if ($value === null) {
            $this->$key = $default;
            $value      = $this->$key;
        }

        return $value;
    }

    /**
     * Set the value of the specified property
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function set($key, $value = null)
    {
        if (in_array($key, $this->property_array)) {
            $this->$key = $value;
        }

        return $this;
    }
}
