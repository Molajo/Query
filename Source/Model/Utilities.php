<?php
/**
 * Model Registry Utilities
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

/**
 * Model Registry Utilities
 *
 * Base - Query - Filters - Utilities - Defaults - Columns - Criteria - Registry
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Utilities extends Filters
{
    /**
     * Get the full contents of the Model Registry
     *
     * @return  mixed
     * @since   1.0
     */
    protected function getModelRegistryAll()
    {
        return $this->model_registry;
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
    protected function getModelRegistryByKey($key = null, $default = null)
    {
        if (isset($this->model_registry[$key])) {
        } else {
            $this->model_registry[$key] = $default;
        }

        return $this->model_registry[$key];
    }

    /**
     * Set Property
     *
     * @param   string $property
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setProperty($property, $value = null)
    {
        $this->model_registry[$property] = $value;

        return $this;
    }

    /**
     * Set Property for Array
     *
     * @param   string $property
     * @param   mixed  $default
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPropertyArray($property, $default = array())
    {
        $this->verifyPropertyExists($property, $default);

        if (is_array($this->model_registry[$property])) {
        } else {
            $this->model_registry[$property] = array();
        }

        return $this;
    }

    /**
     * Verify Property Exists
     *
     * @param   string $property
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function verifyPropertyExists($property, $value = null)
    {
        if (isset($this->model_registry[$property])) {
        } else {
            $this->setProperty($property, $value);
        }

        return $this;
    }
}
