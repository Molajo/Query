<?php
/**
 * Criteria
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Criteria
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Criteria extends Values
{
    /**
     * Process all criteria, filtering with valid attributes array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCriteriaRegistry()
    {
        $this->valid_attributes = $this->data_object->get('valid_criteria_attributes');

        if ($this->processCriteria() === false) {
            return $this;
        }

        $verified_attributes = array();

        foreach ($this->xml->table->criteria->where as $xml_extract) {

            $criteria        = array();
            $attributes      = get_object_vars($xml_extract);
            $item_attributes = ($attributes["@attributes"]);

            foreach ($this->verifyAttributes($item_attributes) as $key => $value) {
                $criteria[$key] = $value;
            }

            $verified_attributes[] = $criteria;
        }

        $this->setModelRegistryElement('criteria', $verified_attributes);

        return $this;
    }

    /**
     * Process Criteria
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processCriteria()
    {
        if (isset($this->xml->table->criteria->where) > 0) {
        } else {
            return false;
        }

        return true;
    }
}
