<?php
/**
 * Resourcexml Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Resourcexml;

use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;

/**
 * Resourcexml Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ResourcexmlFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
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
        $options['product_namespace']        = 'Molajo\\Resource\\Adapter\\Xml';
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

        $this->dependencies['Resource']     = array();
        $this->dependencies['Resourcedata'] = array();
        $this->dependencies['Registry']     = array();

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
        $options['product_name']          = basename(__DIR__);
        $options['product_namespace']     = $this->product_namespace;
        $options['scheme_name']           = 'Xml';
        $options['valid_file_extensions'] = array('.xml');
        $options['handler_options']       = array(
            'data'     => $this->dependencies['Resourcedata'],
            'resource' => $this->dependencies['Resource'],
            'registry' => $this->dependencies['Registry'],
        );

        $this->schedule_factory_methods['Resourceadapter'] = $options;

        return $this->schedule_factory_methods;
    }
}
