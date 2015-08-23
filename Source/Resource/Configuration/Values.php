<?php
/**
 * Values
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Values
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Values extends Foreignkeys
{
    /**
     * Process all values, filtering with valid attributes array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setValuesRegistry()
    {
        $this->valid_attributes = $this->data_object->get('valid_value_attributes');

        if ($this->processValues() === false) {
            return $this;
        }

        $verified_attributes = array();

        foreach ($this->xml->table->values->value as $xml_extract) {

            $values          = array();
            $attributes      = get_object_vars($xml_extract);
            $item_attributes = ($attributes["@attributes"]);

            foreach ($this->verifyAttributes($item_attributes) as $key => $value) {
                $values[$key] = $value;
            }

            $verified_attributes[] = $values;
        }

        $this->setModelRegistryElement('values', $verified_attributes);

        return $this;
    }

    /**
     * Process Values
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processValues()
    {
        if (isset($this->xml->table->values->value) > 0) {
        } else {
            return false;
        }

        return true;
    }
}
