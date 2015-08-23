<?php
/**
 * Resource Query Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Resourcequery;

use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;

/**
 * Resource Query Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ResourcequeryFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
{
    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0.0
     */
    public function __construct(array $options = array())
    {
        $options['product_namespace']        = 'Molajo\\Resource\\Adapter\\Query';
        $options['store_instance_indicator'] = false;
        $options['product_name']             = null;

        parent::__construct($options);
    }

    /**
     * Instantiate a new handler and inject it into the Adapter for the FactoryInterface
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setDependencies(array $reflection = array())
    {
        parent::setDependencies(array());

        $this->dependencies['Database']      = array();
        $this->dependencies['Query']         = array();
        $this->dependencies['Eventcallback'] = array();

        return $this->dependencies;
    }

    /**
     * Factory Method Controller triggers the Factory Method to create the Class for the Service
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        return $this;
    }

    /**
     * Request for array of Factory Methods to be Scheduled
     *
     * @return  object
     * @since   1.0.0
     */
    public function scheduleFactories()
    {
        $options                          = array();
        $options['scheme_name']           = 'Query';
        $options['product_namespace']     = $this->product_namespace;
        $options['valid_file_extensions'] = array();
        $options['handler_options']       = $this->setHandlerOptions();

        $this->schedule_factory_methods['Resourceadapter'] = $options;

        return $this->schedule_factory_methods;
    }

    /**
     * Set Handler Options for Factory
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setHandlerOptions()
    {
        $handler_options = array();

        $handler_options['database']       = $this->dependencies['Database'];
        $handler_options['query']          = $this->dependencies['Query'];
        $handler_options['schedule_event'] = $this->dependencies['Eventcallback'];

        return $handler_options;
    }
}
