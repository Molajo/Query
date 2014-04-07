<?php
/**
 * Resource Query Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Resourcequery;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethodBase;

/**
 * Resource Query Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ResourcequeryFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
{
    /**
     * Constructor
     *
     * @param  array $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['product_namespace'] = 'Molajo\\Resource\\Adapter\\Query';
        $options['product_name']      = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Instantiate a new handler and inject it into the Adapter for the FactoryInterface
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setDependencies(array $reflection = null)
    {
        parent::setDependencies($reflection);

        $this->dependencies['Resource']      = array();
        $this->dependencies['Database']      = array();
        $this->dependencies['Query']         = array();
        $this->dependencies['Runtimedata']   = array();
        $this->dependencies['Eventcallback'] = array();

        return $this->dependencies;
    }

    /**
     * Set Dependencies for Instantiation
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onBeforeInstantiation(array $dependency_values = null)
    {
        parent::onBeforeInstantiation($dependency_values);

        $this->dependencies['base_path']          = $this->options['base_path'];
        $this->dependencies['resource_map']       = $this->readFile(
            $this->options['base_path'] . '/Bootstrap/Files/Output/ResourceMap.json'
        );
        $this->options['Scheme']                  = $this->createScheme();
        $this->dependencies['namespace_prefixes'] = array();
        $this->dependencies['valid_file_extensions']
                                                  = $this->options['Scheme']->getScheme(
            'Query'
        )->include_file_extensions;

        $this->dependencies['query']          = $this->dependencies['Query'];
        $this->dependencies['null_date']      = $this->dependencies['Query']->getNullDate();
        $this->dependencies['current_date']   = $this->dependencies['Query']->getDate();
        $this->dependencies['schedule_event'] = $this->dependencies['Eventcallback'];

        return $this->dependencies;
    }

    /**
     * Follows the completion of the instantiate method
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterInstantiation()
    {
        $this->dependencies['Resource']->setAdapterInstance('Query', $this->product_result);

        return $this;
    }

    /**
     * Create Scheme Instance
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function createScheme()
    {
        $class = 'Molajo\\Resource\\Scheme';

        $input = $this->options['base_path'] . '/Bootstrap/Files/Input/SchemeArray.json';

        try {
            $scheme = new $class ($input);
        } catch (Exception $e) {
            throw new RuntimeException ('Resource Scheme ' . $class
            . ' Exception during Instantiation: ' . $e->getMessage());
        }

        return $scheme;
    }
}
