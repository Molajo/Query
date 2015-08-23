<?php
/**
 * Model Registry Criteria Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

/**
 * Model Registry Criteria Class
 *
 * Registry->Criteria->Columns->Table->Defaults->Utilities->Query->Base
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
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
     * @since   1.0.0
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
     * @since   1.0.0
     */
    protected function setWhereStatementsKeyValue($key, $filter, $key_value)
    {
        $prefix = $this->model_registry['primary_prefix'];

        $this->where('column', $prefix . '.' . $this->model_registry[$key], '=', $filter, $key_value);

        return $this;
    }

    /**
     * MODEL REGISTRY CRITERIA
     *
     * These are either defined within the <model statement or set in the class executing the query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelCriteria()
    {
        if ($this->useModelCriteria() === false) {
            return $this;
        }

        $this->setModelCriteriaWhere('status', 'IN', 'integer', 'criteria_status');
        $this->setModelCriteriaWhere('catalog_type_id', '=', 'integer', 'criteria_catalog_type_id');
        $this->setModelCriteriaWhere('extension_instance_id', '=', 'integer', 'criteria_extension_instance_id');
        $this->setModelCriteriaWhere('menu_id', '=', 'integer', 'criteria_menu_id');

        return $this;
    }

    /**
     * MODEL REGISTRY CRITERIA
     *
     * These are either defined within the <model statement or set in the class executing the query
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function useModelCriteria()
    {
        if ($this->model_registry['use_special_joins'] === 0) {
            return false;
        }

        return true;
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
     * @since   1.0.0
     */
    protected function setModelCriteriaWhere(
        $column_name,
        $operator,
        $filter,
        $model_registry_property
    ) {
        if ($this->testModelCriteriaWhere($column_name, $model_registry_property) === false) {
            return $this;
        }

        $prefix = $this->model_registry['primary_prefix'];

        $left  = $prefix . '.' . $column_name;
        $right = $this->model_registry[$model_registry_property];
        $this->where('column', $left, $operator, $filter, $right);

        return $this;
    }

    /**
     * Test Model Registry Criteria to determine if Where Clause should be built
     *
     * @param   string $column_name
     * @param   string $model_registry_property
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function testModelCriteriaWhere(
        $column_name,
        $model_registry_property
    ) {
        if ($this->useModelCriteriaWhereColumn($column_name) === false
            || $this->useModelCriteriaWhereCriteria($model_registry_property) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Should this Model Criteria be used based on the existence of Column?
     *
     * @param   string $column_name
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function useModelCriteriaWhereColumn($column_name)
    {
        if (is_array($this->model_registry)
            && is_array($this->model_registry['fields'])
            && count($this->model_registry['fields']) > 0) {
        } else {
            return false;
        }

        foreach ($this->model_registry['fields'] as $field) {

            if ($field['name'] === $column_name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Should this Model Criteria be used based on the existence and value of criteria?
     *
     * @param   string $column_name
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function useModelCriteriaWhereCriteria($column_name)
    {
        if (isset($this->model_registry[$column_name])) {
        } else {
            return false;
        }

        if (trim($this->model_registry[$column_name]) === ''
            || $this->model_registry[$column_name] === 0
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
     * @since   1.0.0
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
        if ($this->model_registry['use_special_joins'] === 0
            || count($this->model_registry['criteria']) > 0
        ) {
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
