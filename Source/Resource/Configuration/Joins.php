<?php
/**
 * Joins
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Joins
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Joins extends Criteria
{
    /**
     * Process all fields, filtering with valid attributes array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setJoinsRegistry()
    {
        $this->valid_attributes = $this->data_object->get('valid_join_attributes');

        if ($this->processJoins() === false) {
            return $this;
        }

        $verified_attributes = $this->getJoins();

        $verified_attributes = $this->mergeField('joins', $verified_attributes);

        $this->setModelRegistryElement('Joins', $verified_attributes);

        return $this;
    }

    /**
     * Process Joins
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processJoins()
    {
        if (isset($this->xml->table->joins)) {
        } else {
            return false;
        }

        if (isset($this->xml->table->joins->join)) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Get names of standard fields
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getJoins()
    {
        $join  = array();
        $joins = array();

        foreach ($this->xml->table->joins->join as $item) {
            $attributes      = get_object_vars($item);
            $item_attributes = ($attributes["@attributes"]);
            foreach ($item_attributes as $key => $value) {
                $join[$key] = (string)$value;
            }
            $joins[] = $join;
            $join    = array();
        }

        return $joins;
    }
}
