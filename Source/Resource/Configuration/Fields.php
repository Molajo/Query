<?php
/**
 * Fields
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Fields
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Fields extends Customfields
{
    /**
     * Process all fields, filtering with valid attributes array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setFieldsRegistry()
    {
        $this->valid_attributes = $this->data_object->get('valid_field_attributes');

        if ($this->processFields() === false) {
            return $this;
        }

        $xml_extract         = $this->xml->table->fields->field;
        $verified_attributes = array();

        foreach ($this->getAttributeValues($xml_extract) as $name) {
            $verified_attributes = $this->addField($name, $verified_attributes);
        }

        $verified_attributes = $this->mergeField('fields', $verified_attributes);

        ksort($verified_attributes);

        $this->setModelRegistryElement('fields', $verified_attributes);

        return $this;
    }

    /**
     * Process Fields
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processFields()
    {
        if (isset($this->xml->table->fields->field)) {
            return true;
        }

        return false;
    }

    /**
     * Add Field Definition
     *
     * @param   string $name
     * @param   array  $verified_attributes
     *
     * @return  array
     * @since   1.0.0
     */
    protected function addField($name, array $verified_attributes = array())
    {
        $field = $this->resource->get('field://' . $name);

        $verified_attributes[$name] = $this->verifyAttributes($field);

        return $verified_attributes;
    }
}
