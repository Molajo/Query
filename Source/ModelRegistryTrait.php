<?php
namespace Molajo\Query;

/**
 * Model Registry Trait
 *
 * The Model Registry Trait is designed to be used with the Query Trait.
 * when the Molajo\Query\Model\Registry class is injected into the Molajo\Controller\Query class
 * Access to the Molajo\Query\Builder\Driver is indirectly accessible via the Registry class
 * using the Molajo\Query\Model\Query class (and the Query Trait code) as a Proxy.
 *
 * The instance property $qb is defined within the Query Trait.
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
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
     * Get SQL (optionally setting the SQL)
     *
     * @param   null|string $sql
     *
     * @return  string
     * @since   1.0
     */
    public function getSql($sql = null)
    {
        return $this->qb->getSql($sql);
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
        return $this->qb->getModelRegistry($key, $default);
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
        return $this->qb->setModelRegistry($key, $value);
    }
}
