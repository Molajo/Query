<?php
/**
 * Configuration Data
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

use CommonApi\Exception\RuntimeException;
use Molajo\Resource\Api\ConfigurationDataInterface;

/**
 * Configuration Data
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Data implements ConfigurationDataInterface
{
    /**
     * Valid Data Object Types
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_dataobject_types;

    /**
     * Valid Data Object Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_dataobject_attributes;

    /**
     * Valid Model Types
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_model_types;

    /**
     * Valid Model Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_model_attributes;

    /**
     * Valid Data Types
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_data_types;

    /**
     * Valid Query Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_queryelements_attributes;

    /**
     * Valid Field Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_field_attributes;

    /**
     * Valid Join Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_join_attributes;

    /**
     * Valid Foreignkey Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_foreignkey_attributes;

    /**
     * Valid Criteria Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_criteria_attributes;

    /**
     * Valid Children Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_children_attributes;

    /**
     * Valid Plugin Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_plugin_attributes;

    /**
     * Valid Value Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_value_attributes;

    /**
     * Datalists
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_datalists;

    /**
     * List of Properties
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array
        = array(
            'valid_dataobject_types',
            'valid_dataobject_attributes',
            'valid_model_types',
            'valid_model_attributes',
            'valid_data_types',
            'valid_queryelements_attributes',
            'valid_field_attributes',
            'valid_join_attributes',
            'valid_foreignkey_attributes',
            'valid_criteria_attributes',
            'valid_children_attributes',
            'valid_plugin_attributes',
            'valid_value_attributes',
            'valid_field_attributes_default',
            'valid_datalists'
        );

    /**
     * Constructor
     *
     * @param   string $valid_dataobject_types
     * @param   string $valid_dataobject_types
     * @param   string $valid_dataobject_attributes
     * @param   string $valid_model_types
     * @param   string $valid_model_attributes
     * @param   string $valid_data_types
     * @param   string $valid_queryelements_attributes
     * @param   string $valid_field_attributes
     * @param   string $valid_join_attributes
     * @param   string $valid_foreignkey_attributes
     * @param   string $valid_criteria_attributes
     * @param   string $valid_children_attributes
     * @param   string $valid_plugin_attributes
     * @param   string $valid_value_attributes
     * @param   string $valid_field_attributes_default
     * @param   string $valid_datalists
     *
     *
     * @since   1.0
     */
    public function __construct(
        $valid_dataobject_types,
        $valid_dataobject_attributes,
        $valid_model_types,
        $valid_model_attributes,
        $valid_data_types,
        $valid_queryelements_attributes,
        $valid_field_attributes,
        $valid_join_attributes,
        $valid_foreignkey_attributes,
        $valid_criteria_attributes,
        $valid_children_attributes,
        $valid_plugin_attributes,
        $valid_value_attributes,
        $valid_field_attributes_default,
        $valid_datalists
    ) {

        $this->valid_dataobject_types         = $valid_dataobject_types;
        $this->valid_dataobject_attributes    = $valid_dataobject_attributes;
        $this->valid_model_types              = $valid_model_types;
        $this->valid_model_attributes         = $valid_model_attributes;
        $this->valid_data_types               = $valid_data_types;
        $this->valid_queryelements_attributes = $valid_queryelements_attributes;
        $this->valid_field_attributes         = $valid_field_attributes;
        $this->valid_join_attributes          = $valid_join_attributes;
        $this->valid_foreignkey_attributes    = $valid_foreignkey_attributes;
        $this->valid_criteria_attributes      = $valid_criteria_attributes;
        $this->valid_children_attributes      = $valid_children_attributes;
        $this->valid_plugin_attributes        = $valid_plugin_attributes;
        $this->valid_value_attributes         = $valid_value_attributes;
        $this->valid_field_attributes_default = $valid_field_attributes_default;
        $this->valid_datalists                = $valid_datalists;
    }

    /**
     * Set the value of a specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new RuntimeException('Configuration Data Object: Set invalid key: ' . $key);
        }

        $this->$key = $value;

        return;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function get($key, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new RuntimeException('Configuration Data Object: Set invalid key: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }
}
