<?php
/**
 * Data Object Configuration
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

use CommonApi\Exception\RuntimeException;
use CommonApi\Query\ConfigurationInterface;
use CommonApi\Query\RegistryInterface;
use CommonApi\Resource\ResourceInterface;

/**
 * Data Object Configuration
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Dataobject extends Includes implements ConfigurationInterface
{
    /**
     * Constructor
     *
     * @param object            $data_object
     * @param RegistryInterface $registry
     * @param ResourceInterface $resource
     *
     * @since  1.0.0
     */
    public function __construct(
        $data_object,
        RegistryInterface $registry,
        ResourceInterface $resource
    ) {
        parent::__construct($data_object, $registry, $resource);
    }

    /**
     * Load registry for requested model resource, returning name of registry collection
     *
     * @param   string $model_type
     * @param   string $model_name
     * @param   object $xml
     *
     * @return  array
     * @since   1.0.0
     */
    public function getConfiguration($model_type, $model_name, $xml)
    {
        $this->setModelNames($model_type, $model_name);
        $this->setXml($xml);
        $this->getIncludeCode();
        $this->createRegistry();
        $this->setModelRegistry();

        $this->registry->sort($this->model_registry);

        return $this->registry->get($this->model_registry);
    }

    /**
     * Store Configuration Data in Registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelRegistry()
    {
        $this->setModelRegistryKeys($this->data_object->get('valid_data_object_attributes'));

        $this->registry->set($this->model_registry, 'data_object', 'data_object');
        $this->registry->set($this->model_registry, 'model_type', 'data_object');
        $this->registry->set($this->model_registry, 'model_name', $this->registry->get($this->model_registry, 'name'));

        return $this;
    }

    /**
     * Set Model Registry Keys
     *
     * @param   array $valid_array
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModelRegistryKeys(array $valid_array = array())
    {
        foreach ($this->xml->attributes() as $key => $value) {

            if (in_array((string)$key, $valid_array)) {
                $this->registry->set($this->model_registry, $key, (string)$value);

            } else {
                throw new RuntimeException(
                    'Configuration: setDataObjectRegistry encountered Invalid data_object Attributes ' . $key
                );
            }
        }

        return $this;
    }
}
