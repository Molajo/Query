<?php
/**
 * Abstract Model Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Model;

use CommonApi\Database\DatabaseInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Model\ModelInterface;

/**
 * Abstract Model Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
abstract class Model implements ModelInterface
{
    /**
     * Database Instance
     *
     * @var    object   CommonApi\Database\DatabaseInterface
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
    protected $property_array = array(
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
}
