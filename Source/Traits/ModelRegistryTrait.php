<?php
namespace Molajo\Query;

/**
 * Model Registry Trait
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
trait ModelRegistryTrait
{
    /**
     * Model Registry Instance
     *
     * @var     object  CommonApi\Query\ModelRegistryInterface
     * @since   1.0
     */
    protected $mr;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $model_registry = null;

    /**
     * Get SQL (optionally setting the SQL)
     *
     * @param   null|string $sql
     *
     * @return  string
     * @since   1.0
     */
    public function getSql($sql = null)
    {
        return $this->mr->getSql($sql);
    }

    /**
     * Get the current value (or default) of the Model Registry
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function getModelRegistry($key, $default = null)
    {
        return $this->mr->getModelRegistry($key, $default);
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
        return $this->mr->setModelRegistry($key, $value);
    }
}
