<?php
/**
 * Configuration: Xml: Dataobject Handler
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

use CommonApi\Exception\RuntimeException;
use CommonApi\Resource\ResourceInterface;
use Molajo\Resource\Api\ConfigurationDataInterface;
use Molajo\Resource\Api\ConfigurationInterface;
use Molajo\Resource\Api\RegistryInterface;

/**
 * Configuration: Xml: Dataobject Handler
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Dataobject extends AbstractAdapter implements ConfigurationInterface
{
    /**
     * Constructor
     *
     * @param ConfigurationDataInterface $dataobject
     * @param RegistryInterface          $registry
     * @param ResourceInterface          $resource
     *
     * @since  1.0
     */
    public function __construct(
        ConfigurationDataInterface $dataobject,
        RegistryInterface $registry,
        ResourceInterface $resource
    ) {
        parent::__construct($dataobject, $registry, $resource);
    }

    /**
     * Load registry for requested model resource, returning name of registry collection
     *
     * @param   string $model_type
     * @param   string $model_name
     * @param   object $xml
     *
     * @return  string  Name of registry model
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getConfiguration($model_type, $model_name, $xml)
    {
        $model_registry = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

        $xml = $this->getIncludeCode($xml);
        if ($xml === false) {
            throw new RuntimeException(
                'Configuration: getDataobject cannot process XML file for Model Type: '
                . $model_type . ' Model Name: ' . $model_name
            );
        }

        if (isset($xml->model)) {
            $xml = $xml->model;
        }

        $this->registry->createRegistry($model_registry);

        $this->setModelRegistry($model_registry, $xml);

        $this->registry->sort($model_registry);

        return $this->registry->getArray($model_registry);
    }

    /**
     * Store Configuration Data in Registry
     *
     * @param   string $model_registry
     * @param   object $xml
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setModelRegistry($model_registry, $xml)
    {
        $doArray = $this->dataobject->get('valid_dataobject_attributes');

        foreach ($xml->attributes() as $key => $value) {
            if (in_array((string)$key, $doArray)) {
                $this->registry->set($model_registry, $key, (string)$value);
            } else {
                throw new RuntimeException(
                    'Configuration: setDataobjectRegistry encountered Invalid Dataobject Attributes ' . $key
                );
            }
        }

        $this->registry->set($model_registry, 'data_object', 'Dataobject');
        $this->registry->set($model_registry, 'model_type', 'Dataobject');
        $this->registry->set(
            $model_registry,
            'model_name',
            $this->registry->get($model_registry, 'name')
        );

        return $this;
    }
}
