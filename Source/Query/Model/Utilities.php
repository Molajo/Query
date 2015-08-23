<?php
/**
 * Model Registry Utilities
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

/**
 * Model Registry Utilities
 *
 * Registry->Criteria->Columns->Table->Defaults->Utilities->Query->Base
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Utilities extends Query
{
    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function getModelRegistryByKey($key = null, $default = null)
    {
        if (isset($this->model_registry[$key])) {
        } else {
            $this->model_registry[$key] = $default;
        }

        return $this->model_registry[$key];
    }

    /**
     * Set Property
     *
     * @param   string $property
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setProperty($property, $value = null)
    {
        $this->model_registry[$property] = $value;

        return $this;
    }

    /**
     * Set Property for Array
     *
     * @param   string $property
     * @param   mixed  $default
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPropertyArray($property, $default = array())
    {
        $this->verifyPropertyExists($property, $default);

        if (is_array($this->model_registry[$property])) {
        } else {
            $this->model_registry[$property] = array();
        }

        return $this;
    }

    /**
     * Verify Property Exists
     *
     * @param   string $property
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function verifyPropertyExists($property, $value = null)
    {
        if (isset($this->model_registry[$property])) {
        } else {
            $this->setProperty($property, $value);
        }

        return $this;
    }

    /**
     * COMMON CODE
     *
     * Set "Where Pair" Left - Operator - Right
     *
     * @param   string $join_to_item_alias
     * @param   string $join_to_item
     * @param   string $operator
     * @param   string $join_with_item_alias
     * @param   string $join_with_item
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setWherePair(
        $join_to_item_alias,
        $join_to_item,
        $operator,
        $join_with_item_alias,
        $join_with_item
    ) {
        /** Left */
        list($join_to_filter, $join_to_value) = $this->setWhereElement($join_to_item_alias, $join_to_item);

        /** Operator */
        list($operator) = $this->setWhereOperator($operator);

        /** Right */
        list($join_with_filter, $join_with_value) = $this->setWhereElement($join_with_item_alias, $join_with_item);

        /** Set the Where Statement */
        $this->where($join_to_filter, $join_to_value, $operator, $join_with_filter, $join_with_value);

        return $this;
    }

    /**
     * Set Where Operator
     *
     * @param   string $operator
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setWhereOperator($operator)
    {
        if (in_array($operator, $this->operator_array)) {
            return $operator;
        }

        $operator = '=';

        return $operator;
    }

    /**
     * Add Model Registry Criteria to Query
     *
     * @param   string $join_with_item_alias
     * @param   string $join_item
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setWhereElement($join_with_item_alias, $join_item)
    {
        if (isset($this->query_where_property_array[$join_item])) {
            return $this->setWhereElementProperty($join_item);
        }

        if (is_numeric($join_item)) {
            return $this->setWhereElementNumericValue($join_item);
        }

        return $this->setWhereElementTableColumn($join_with_item_alias, $join_item);
    }

    /**
     * Where element is a named property (ex. APPLICATION_ID)
     *
     * @param   string $join_item
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setWhereElementProperty($join_item)
    {
        $key = $this->query_where_property_array[$join_item];

        if (isset($this->model_registry[$key])) {
            $value = $this->model_registry[$key];
        } else {
            $value = $this->$key;
        }

        return array('integer', $value);
    }

    /**
     * Where element is a numeric value
     *
     * @param   string $join_item
     *
     * @return  string[]
     * @since   1.0.0
     */
    protected function setWhereElementNumericValue($join_item)
    {
        return array('integer', $join_item);
    }

    /**
     * Where element is a table column
     *
     * @param   string $join_with_item_alias
     * @param   string $join_item
     *
     * @return  string[]
     * @since   1.0.0
     */
    protected function setWhereElementTableColumn($join_with_item_alias, $join_item)
    {
        return array('column', $join_with_item_alias . '.' . $join_item);
    }

    /**
     * Set Model Registry Limits
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelRegistryLimits()
    {
        if (count($this->model_registry['use_pagination']) === 0) {
        } else {
            return $this;
        }

        $this->setOffsetAndLimit($this->model_registry['offset'], $this->model_registry['limit']);

        return $this;
    }
}
