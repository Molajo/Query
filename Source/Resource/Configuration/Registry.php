<?php
/**
 * Registry
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

use CommonApi\Query\RegistryInterface;

/**
 * Registry - case insensitive names named pairs
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Registry implements RegistryInterface
{
    /**
     * Array containing registries and data
     *
     * @var    array
     * @since  1.0.0
     */
    protected $registry = array();

    /**
     * Verifies if registry, or registry key, exists
     *
     * @param   string      $name
     * @param   NULL|string $key
     *
     * @return  boolean
     * @since   1.0.0
     */
    public function exists($name, $key = null)
    {
        list($name, $key) = $this->initialiseNameKey($name, $key);

        if ($this->existsName($name) === false) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        return $this->existsNameKey($name, $key);
    }

    /**
     * Create a registry
     *
     * @param   string $name
     *
     * @return  array
     * @since   1.0.0
     */
    public function createRegistry($name)
    {
        $name = strtolower($name);

        $this->registry[$name] = array();

        return $this->registry[$name];
    }

    /**
     * Get Registry Data
     *
     * @param   string $name
     * @param   string $key
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function get($name, $key = null)
    {
        list($name, $key) = $this->initialiseNameKey($name, $key);

        if ($this->exists($name, $key) === false) {
            return null;
        }

        $registry = $this->registry[$name];

        if ($key === null) {
            return $registry;
        }

        if (isset($registry[$key])) {
            return $registry[$key];
        }

        return null;
    }

    /**
     * Sets the value for a specific registry $key $value pair
     *
     * @param   string $name
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  $this
     * @since   1.0.0
     */
    public function set($name, $key, $value = null)
    {
        list($name, $key) = $this->initialiseNameKey($name, $key);

        if ($this->exists($name) === false) {
            $registry = $this->createRegistry($name);
        } else {
            $registry = $this->registry[$name];
        }

        $registry[$key]        = $value;
        $this->registry[$name] = $registry;

        return $this;
    }

    /**
     * Sort Registry
     *
     * @param   string $name
     *
     * @return  $this
     * @since   1.0.0
     */
    public function sort($name)
    {
        $name = strtolower($name);

        $registry = $this->registry[$name];

        ksort($registry);

        $this->registry[$name] = $registry;

        return $this;
    }

    /**
     * Verify Registry exists
     *
     * @param   string $name
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function existsName($name)
    {
        $name = strtolower($name);

        if (isset($this->registry[$name])) {
            return true;
        }

        return false;
    }

    /**
     * Verify Registry Key exists
     *
     * @param   string $name
     * @param   string $key
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function existsNameKey($name, $key)
    {
        list($name, $key) = $this->initialiseNameKey($name, $key);

        $registry = $this->registry[$name];

        if (is_array($registry) && count($registry) > 0) {
        } else {
            return false;
        }

        if (isset($registry[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Set Name and Key to lowercase
     *
     * @param   string $name
     * @param   string $key
     *
     * @return  array
     * @since   1.0.0
     */
    protected function initialiseNameKey($name, $key)
    {
        $name = strtolower($name);

        if ($key === null) {
        } else {
            $key = strtolower($key);
        }

        return array($name, $key);
    }
}
