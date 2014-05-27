<?php
/**
 * Query Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Query;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethodBase;

/**
 * Query Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class QueryFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
{
    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['product_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['product_namespace']        = 'Molajo\Query\Driver';

        parent::__construct($options);
    }

    /**
     * Define dependencies or use dependencies automatically defined by base class using Reflection
     *
     * @param   array $reflection
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setDependencies(array $reflection = null)
    {
        $reflection = array();

        parent::setDependencies($reflection);

        $this->dependencies                 = array();
        $this->dependencies['Fieldhandler'] = array();
        $this->dependencies['Resource']     = array();
        $this->dependencies['Database']     = array();

        return $this->dependencies;
    }

    /**
     * Set Dependency values
     *
     * @param   array $dependency_values (ignored in Service Item Adapter, based in from handler)
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeInstantiation(array $dependency_values = null)
    {
        parent::onBeforeInstantiation($dependency_values);

        $this->dependencies['database_prefix'] = 'molajo_';

        $this->dependencies['adapter'] = $this->getAdapter('Mysql');

        return $this;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $class = $this->product_namespace;

        try {
            $this->product_result = new $class(
                $this->dependencies['adapter']
            );

        } catch (Exception $e) {
            throw new RuntimeException('Query Factory: Could not instantiate Driver: ' . $class);
        }

        return $this;
    }

    /**
     * Get the Query Adapter
     *
     * @param   string $type
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getAdapter($type)
    {
        $class = 'Molajo\\Query\\Adapter\\' . $type;

        try {
            return new $class(
                $this->dependencies['Fieldhandler'],
                $this->dependencies['database_prefix'],
                $this->dependencies['Database']
            );

        } catch (Exception $e) {
            throw new RuntimeException('Query: Could not instantiate Handler: ' . $class);
        }
    }
}
