<?php
/**
 * Children
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Children
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Children extends Plugins
{
    /**
     * Process all children, filtering with valid attributes array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setChildrenRegistry()
    {
        $this->valid_attributes = $this->data_object->get('valid_children_attributes');

        if ($this->processChildren() === false) {
            return $this;
        }

        $verified_attributes = array();

        foreach ($this->xml->table->children->child as $xml_extract) {

            $children        = array();
            $attributes      = get_object_vars($xml_extract);
            $item_attributes = ($attributes["@attributes"]);

            foreach ($this->verifyAttributes($item_attributes) as $key => $value) {
                $key            = strtolower($key);
                $value          = strtolower($value);
                $children[$key] = $value;
            }

            $verified_attributes[] = $children;
        }

        $this->setModelRegistryElement('children', $verified_attributes);

        return $this;
    }

    /**
     * Process Children
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processChildren()
    {
        if (isset($this->xml->table->children->child) > 0) {
        } else {
            return false;
        }

        return true;
    }
}
