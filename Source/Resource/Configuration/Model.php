<?php
/**
 * Model Configuration
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
 * Model Configuration
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Model extends Includes implements ConfigurationInterface
{
    /**
     * Set Registry Methods
     *
     * @var    array
     * @since  1.0.0
     */
    protected $set_registry_methods
        = array(
            'setFieldsRegistry',
            'setCustomfieldsRegistry',
            'setJoinsRegistry',
            'setCriteriaRegistry',
            'setValuesRegistry',
            'setForeignkeysRegistry',
            'setChildrenRegistry',
            'setPluginsRegistry'
        );

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
     * @return  string
     * @since   1.0.0
     */
    public function getConfiguration($model_type, $model_name, $xml)
    {
        /** Initialise and create model registry */
        $this->custom_field_groups = array();
        $this->setModelNames($model_type, $model_name);
        $this->setXml($xml);
        $this->createRegistry();
        $this->getIncludeCode();
        $this->setModelRegistry();

        /** Expand, given inheritance and set data object */
        $this->inheritDefinition();
        $this->setDataObject();

        /** Define Elements */
        foreach ($this->set_registry_methods as $method) {
            $this->$method();
        }

        /** Finalize */
        $this->setCustomfieldsElement();

        /** Sort and Return */
        $this->registry->sort($this->model_registry);
        $registry = $this->registry->get($this->model_registry);

        return $this->unsetFields($registry);
    }

    /**
     * Store Configuration Data in Registry
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setModelRegistry()
    {
        $model_array = $this->data_object->get('valid_model_attributes');

        foreach ($this->xml->attributes() as $key => $value) {

            if (in_array($key, $model_array)) {
                $this->registry->set($this->model_registry, $key, (string)$value);
            } else {
                throw new RuntimeException(
                    'Configuration: setModelRegistry encountered Invalid Model Attribute ' . $key
                );
            }
        }

        $name = $this->registry->get($this->model_registry, 'name');

        $this->registry->set($this->model_registry, 'model_name', $name);

        return $this;
    }

    /**
     * Set Data Object
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setDataObject()
    {
        $data_object = $this->registry->get($this->model_registry, 'data_object', '');

        if ($data_object === '') {
            return $this;
        }

        $data_object_registry = ucfirst(strtolower($data_object)) . 'Dataobject';

        if ($this->registry->exists($data_object_registry)) {
            $results = $this->registry->get($data_object_registry);
        } else {
            $results = $this->resource->get('xml://Molajo//Model//Dataobject//'
                . ucfirst(strtolower($data_object)) . '.xml');
        }

        foreach ($results as $key => $value) {
            $this->registry->set($this->model_registry, 'data_object_' . $key, $value);
        }

        return $this;
    }

    /**
     * Set Data Object
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setDefaultDataobject()
    {
        $data_object = 'Database';

        $this->registry->set($this->model_registry, 'data_object', $data_object);

        return $data_object;
    }

    /**
     * Set Data Object
     *
     * @param   array $registry
     *
     * @return  string
     * @since   1.0.0
     */
    protected function unsetFields($registry)
    {
        if (isset($registry['extends'])) {
            unset($registry['extends']);
        }

        return $registry;
    }

    /**
     * Set Custom Fields Element
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCustomfieldsElement()
    {
        $groups = array_unique($this->custom_field_groups);
        sort($groups);
        $this->setModelRegistryElement('customfieldgroups', $groups);

        return $this;
    }
}
