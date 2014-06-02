<?php
/**
 * Model Registry Criteria Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

/**
 * Model Registry Criteria Class
 *
 * Base - Query - Utilities - Defaults - Table - Columns - Criteria - Registry
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Criteria extends Columns
{
    /**
     * KEY CRITERIA
     *
     * Set Where Statements for ID or Name Keys
     *
     * @return  $this
     * @since   1.0
     */
    protected function setKeyCriteria()
    {
        $primary_key = $this->model_registry['primary_key_value'];
        $name_key    = $this->model_registry['name_key_value'];

        if ((int)$primary_key > 0) {
            return $this->setWhereStatementsKeyValue('primary_key', 'integer', $primary_key);
        }

        if (trim($name_key) === '') {
            return $this;
        }

        $this->setWhereStatementsKeyValue('name_key', 'string', $name_key);

        return $this;
    }

    /**
     * Set Where Statements: Key Provided
     *
     * @param   string $key
     * @param   string $filter
     * @param   string $key_value
     *
     * @return  $this
     * @since   1.0
     */
    protected function setWhereStatementsKeyValue($key, $filter, $key_value)
    {
        $this->where(
            'column',
            $this->model_registry['primary_prefix'] . '.' . $this->model_registry[$key],
            '=',
            $filter,
            $key_value
        );

        return $this;
    }

    /**
     * MODEL REGISTRY CRITERIA
     *
     * These are either defined within the <model statement or set in the class executing the query
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelCriteria()
    {
        $this->setModelCriteriaWhere('status', 'IN', 'integer', 'criteria_status');
        $this->setModelCriteriaWhere('catalog_type_id', '=', 'integer', 'criteria_catalog_type_id');
        $this->setModelCriteriaWhere('extension_instance_id', '=', 'integer', 'criteria_extension_instance_id');
        $this->setModelCriteriaWhere('menu_id', '=', 'integer', 'criteria_menu_id');

        return $this;
    }

    /**
     * Model Registry Criteria: Builds Where Clause
     *
     * @param   string $column_name
     * @param   string $operator
     * @param   string $filter
     * @param   string $model_registry_property
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelCriteriaWhere(
        $column_name,
        $operator,
        $filter,
        $model_registry_property
    ) {

        if ($this->useModelCriteriaWhere($column_name) === false) {
            return $this;
        }

        $this->where(
            'column',
            $this->model_registry['primary_prefix'] . '.' . $column_name,
            $operator,
            $filter,
            $this->model_registry[$model_registry_property]
        );

        return $this;
    }

    /**
     * Should this Model Criteria be used?
     *
     * @param   string $column_name
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useModelCriteriaWhere($column_name)
    {
        if (isset($this->model_registry[$column_name])) {
        } else {
            return false;
        }

        if ($this->model_registry[$column_name] === ''
            || (int)$this->model_registry[$column_name] === 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * MODEL REGISTRY CRITERIA ARRAY
     *
     *      <criteria>
     *          <where name="a.enabled"
     *              connector="="
     *              value="1"/>
     *          <where name="a.redirect_to_id"
     *              connector="="
     *              value="0"/>
     *      </criteria>
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelCriteriaArrayCriteria()
    {
        if ($this->useModelCriteriaArray() === false) {
            return $this;
        }

        foreach ($this->model_registry['criteria'] as $item) {
            $this->setModelRegistryCriteriaArrayItem($item);
        }

        return $this;
    }

    /**
     * Model Registry Criteria Array: Use or Don't Use
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function useModelCriteriaArray()
    {
        if (count($this->model_registry['criteria']) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Model Registry Criteria Array: Use or Don't Use
     *
     * @param   array $item
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelRegistryCriteriaArrayItem($item)
    {
        if (isset($item['value'])) {
            $this->where('column', $item['name'], $item['connector'], 'integer', (int)$item['value']);

        } elseif (isset($item['name2'])) {
            $this->where('column', $item['name'], $item['connector'], 'column', $item['name2']);
        }

        return $this;
    }
}
