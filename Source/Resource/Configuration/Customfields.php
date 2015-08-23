<?php
/**
 * Customfields
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Customfields
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Customfields extends Joins
{
    /**
     * Custom Field Groups
     *
     * @var    array
     * @since  1.0.0
     */
    protected $custom_field_groups = array();

    /**
     * Process all fields, filtering with valid attributes array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCustomfieldsRegistry()
    {
        if ($this->processCustomfields() === false) {
            return $this;
        }

        $this->valid_attributes = $this->data_object->get('valid_field_attributes');

        foreach ($this->xml->customfields->customfield as $item) {

            $attributes                  = get_object_vars($item);
            $item_attributes             = ($attributes["@attributes"]);
            $custom_field_group          = (string)$item_attributes['name'];
            $this->custom_field_groups[] = $custom_field_group;
            $custom_field_attributes     = ($attributes["field"]);

            $customfields = array();

            foreach ($custom_field_attributes as $field) {

                $attributes          = get_object_vars($field);
                $field_attributes    = ($attributes["@attributes"]);
                $name                = $field_attributes['name'];
                $customfield         = $this->resource->get('field://' . $name);
                $verified_attributes = $this->verifyAttributes($customfield);

                $customfields[$name] = $verified_attributes;
            }

            $customfields = $this->mergeField($custom_field_group, $customfields);

            ksort($customfields);

            $this->setModelRegistryElement($custom_field_group, $customfields);
        }

        return $this;
    }

    /**
     * Process all fields, filtering with valid attributes array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCustomfield($field, array $customfields = array())
    {
        $attributes          = get_object_vars($field);
        $field_attributes    = ($attributes["@attributes"]);
        $name                = $field_attributes['name'];
        $customfield         = $this->resource->get('field://' . $name);
        $verified_attributes = $this->verifyAttributes($customfield);

        $customfields[$name] = $verified_attributes;

        return $this;
    }

    /**
     * Process Custom Fields
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processCustomfields()
    {
        if (isset($this->xml->customfields->customfield)) {
            return true;
        }

        return false;
    }
}
