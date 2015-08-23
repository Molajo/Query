<?php
/**
 * Query Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Query;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;

/**
 * Query Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
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
        $options['product_namespace']        = 'Molajo\\Query\\Database';

        parent::__construct($options);
    }

    /**
     * Define dependencies or use dependencies automatically defined by base class using Reflection
     *
     * @param   array $reflection
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setDependencies(array $reflection = array())
    {
        parent::setDependencies(array());

        $this->dependencies                 = array();
        $this->dependencies['Fieldhandler'] = array();
        $this->dependencies['Runtimedata']  = array();
        $this->dependencies['Resource']     = array();
        $this->dependencies['Database']     = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $query_builder = $this->getQueryBuilder();

        try {
            $this->product_result = new $this->product_namespace(
                $query_builder,
                $this->dependencies['Database']
            );

        } catch (Exception $e) {
            throw new RuntimeException('Query Factory: Could not instantiate Driver: ' . $this->product_namespace);
        }

        return $this;
    }

    /**
     * Get Query Model Registry
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getQueryBuilder()
    {
        $query_proxy = $this->getQueryProxy();
        $registry    = $this->getRegistry($query_proxy, array());

        $class = 'Molajo\\Query\\QueryBuilder';

        try {
            return new $class($registry);

        } catch (Exception $e) {
            throw new RuntimeException('QueryFactoryMethod: Could not instantiate getQueryBuilder: ' . $class);
        }
    }

    /**
     * Get the Query Proxy
     *
     * @return  object CommonApi\Query\QueryInterface
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getQueryProxy()
    {
        $adapter = $this->getQueryAdapter();
        $proxy   = 'Molajo\\Query\\QueryProxy';

        try {
            return new $proxy($adapter);

        } catch (Exception $e) {
            throw new RuntimeException(
                'QueryFactoryMethod: Could not instantiate getQueryAdapter: ' . $adapter);
        }
    }

    /**
     * Get the Query Adapter for DB type
     *
     * @return  object CommonApi\Query\QueryInterface
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getQueryAdapter()
    {
        $prefix = $this->dependencies['Runtimedata']->site->database->prefix;
        $dbtype = ucfirst(strtolower($this->dependencies['Runtimedata']->site->database->type));

        $adapter = 'Molajo\\Query\\Adapter\\' . $dbtype;

        try {
            return new $adapter($this->dependencies['Fieldhandler'], $prefix);

        } catch (Exception $e) {
            throw new RuntimeException(
                'QueryFactoryMethod: Could not instantiate getQueryAdapter: ' . $adapter);
        }
    }

    /**
     * Get the Query Model Registry
     *
     * @param   object  \CommonApi\Query\QueryInterface
     * @param   array $model_registry
     *
     * @return  object  CommonApi\Query\QueryBuilderInterface
     * @since   1.0.0
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
