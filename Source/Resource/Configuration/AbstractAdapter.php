<?php
/**
 * Abstract Adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

use CommonApi\Query\ConfigurationInterface;
use CommonApi\Query\RegistryInterface;
use CommonApi\Resource\ResourceInterface;

/**
 * Abstract Adapter
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class AbstractAdapter implements ConfigurationInterface
{
    /**
     * Data Object Instance
     *
     * @var    object
     * @since  1.0.0
     */
    protected $data_object;

    /**
     * Registry
     *
     * @var    object CommonApi\Query\RegistryInterface
     * @since  1.0.0
     */
    protected $registry;

    /**
     * Resource Instance
     *
     * @var    object CommonApi\Resource\ResourceInterface
     * @since  1.0.0
     */
    protected $resource;

    /**
     * Model Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_type;

    /**
     * Model Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_name;

    /**
     * Model Registry
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_registry;

    /**
     * Xml
     *
     * @var    object
     * @since  1.0.0
     */
    protected $xml;

    /**
     * Valid Attributes
     *
     * @var    array
     * @since  1.0.0
     */
    protected $valid_attributes = array();

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
        $this->data_object = $data_object;
        $this->registry    = $registry;
        $this->resource    = $resource;
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
    abstract public function getConfiguration($model_type, $model_name, $xml);

    /**
     * Set Model Names
     *
     * @param   string $model_type
     * @param   string $model_name
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelNames($model_type, $model_name)
    {
        $this->model_type     = $model_type;
        $this->model_name     = $model_name;
        $this->model_registry = ucfirst(strtolower($this->model_name))
            . ucfirst(strtolower($this->model_type));

        return $this;
    }

    /**
     * Set Xml
     *
     * @param   object $xml
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }

    /**
     * Create Registry and set model names
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function createRegistry()
    {
        $this->registry->createRegistry($this->model_registry);

        $this->registry->set($this->model_registry, 'model_name', $this->model_name);
        $this->registry->set($this->model_registry, 'model_type', $this->model_type);
        $this->registry->set($this->model_registry, 'model_registry_name', $this->model_registry);

        return $this;
    }

    /**
     * Set Model Registry for a specific element (ex. fields, joins, join_fields, etc.)
     *
     * @param   string $key
     * @param   mixed  $attributes
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelRegistryElement($key, $verified)
    {
        $this->registry->set($this->model_registry, $key, $verified);

        return true;
    }

    /**
     * Get Values for the specified attribute
     *
     * @param   object $xml_extract
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getAttributeValues($xml_extract)
    {
        $values = array();

        foreach ($xml_extract as $item) {

            $attributes      = get_object_vars($item);
            $item_attributes = ($attributes["@attributes"]);

            foreach ($item_attributes as $key => $value) {
                $values[] = (string)$value;
            }
        }

        return $values;
    }

    /**
     * Verify Attributes
     *
     * @param   array $values
     * @param   array $valid_attributes
     *
     * @return  $field
     * @since   1.0.0
     */
    protected function verifyAttributes($values)
    {
        $verified_attributes = array();

        foreach ($this->valid_attributes as $attribute) {
            if (isset($values[$attribute])) {
                $verified_attributes[$attribute] = $values[$attribute];
            }
        }

        return $verified_attributes;
    }

    /**
     * Merge Field
     *
     * @param   string $key
     * @param   array  $verified_attributes
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function mergeField($key, array $verified_attributes = array())
    {
        $existing = $this->registry->get($this->model_registry, $key);

        if (is_array($existing)) {
        } else {
            $existing = array();
        }

        foreach ($verified_attributes as $key => $value) {

            if (isset($existing[$key])) {
            } else {
                $existing[$key] = $value;
            }
        }

        return $existing;
    }
}
