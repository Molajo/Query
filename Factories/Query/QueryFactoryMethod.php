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
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;
use Molajo\Query\QueryProxy;

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
        $options['product_namespace']        = 'Molajo\\Query\\QueryBuilder';

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
    public function setDependencies(array $reflection = array())
    {
        parent::setDependencies(array());

        $this->dependencies                 = array();
        $this->dependencies['Fieldhandler'] = array();
        $this->dependencies['Resource']     = array();
        $this->dependencies['Database']     = array();

        return $this->dependencies;
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
        $query_proxy = $this->getQueryProxy('MySQL');
        $model_registry = array();
        $registry = $this->getRegistry($query_proxy, $model_registry);

        $class = $this->product_namespace;

        try {
            $this->product_result = new $class($registry);

        } catch (Exception $e) {
            throw new RuntimeException('Query Factory: Could not instantiate Driver: ' . $class);
        }

        return $this;
    }

    /**
     * Get the Query Proxy
     *
     * @param   string $type
     *
     * @return  object CommonApi\Query\QueryInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getQueryProxy($type = 'MySql')
    {
        $class = 'Molajo\\Query\\Adapter\\' . $type;

        try {
            $query_class = new $class($this->dependencies['Fieldhandler'], 'molajo_', $this->dependencies['Database']);

            return new QueryProxy($query_class);

        } catch (Exception $e) {
            throw new RuntimeException('QueryFactoryMethod: Could not instantiate getQueryProxy: ' . $class);
        }
    }

    /**
     * Get the Query Model Registry
     *
     * @param   object  \CommonApi\Query\QueryInterface
     * @param   array   $model_registry
     *
     * @return  object  CommonApi\Query\QueryBuilderInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getRegistry($query_proxy, array $model_registry = array())
    {
        $class = 'Molajo\\Query\\Model\\Registry';

        try {
            return new $class($query_proxy, $model_registry);

        } catch (Exception $e) {
            throw new RuntimeException('QueryFactoryMethod: Could not instantiate getRegistry: ' . $class);
        }
    }
}
