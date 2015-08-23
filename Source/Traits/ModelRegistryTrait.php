<?php
namespace Molajo\Query;

/**
 * Model Registry Trait
 *
 * The instance property $query is defined within the Query Trait which can be used
 * alone but must always be used with this trait.
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
trait ModelRegistryTrait
{
    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $model_registry = null;

    /**
     * Get the current value (or default) of the Model Registry
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function getModelRegistry($key = null, $default = null)
    {
        return $this->query->getModelRegistry($key, $default);
    }

    /**
     * Set the value of the specified Model Registry
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setModelRegistry($key, $value = null)
    {
        return $this->query->setModelRegistry($key, $value);
    }

    /**
     * Set the value of the specified Model Registry Property
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  $this
     * @since   1.0.0
     */
    public function set($key, $value = null)
    {
        return $this->query->set($key, $value);
    }
}
