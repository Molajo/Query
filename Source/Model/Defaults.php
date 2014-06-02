<?php
/**
 * Model Registry Defaults
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

use CommonApi\Query\QueryInterface;

/**
 * Model Registry Defaults
 *
 * Base - Query - Utilities - Defaults - Columns - Criteria - Registry
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Defaults extends Utilities
{
    /**
     * List of Controller Methods
     *
     * @var    array
     * @since  1.0
     */
    protected $method_array
        = array(
            'setModelRegistryBase',
            'setModelRegistryDefaultsGroup',
            'setModelRegistryCriteriaValues',
            'setModelRegistryDefaultsKeys',
            'setModelRegistryDefaultsFields',
            'setModelRegistryDefaultsTableName',
            'setModelRegistryDefaultsPrimaryPrefix',
            'setModelRegistryDefaultsQueryObject',
            'setModelRegistryDefaultsCriteriaArray',
            'setModelRegistryDefaultsJoins',
            'setModelRegistryDefaultLimits',
            'setModelRegistryPaginationCrossEdits'
        );

    /**
     * Class Constructor
     *
     * @param  array $model_registry
     *
     * @since  1.0.0
     */
    public function __construct(
        QueryInterface $query,
        array $model_registry = array()
    ) {
        $this->setModelRegistryDefaults($model_registry);

        parent::__construct($query);
    }

    /**
     * Set Default Values for Model Registry
     *
     * @param   array $model_registry
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setModelRegistryDefaults($model_registry)
    {
        $this->model_registry = $model_registry;

        foreach ($this->method_array as $method) {
            $this->$method();
        }

        return $this;
    }

    /**
     * Set Default Values for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryBase()
    {
        if (isset($this->model_registry['query_object'])) {
        } else {
            $this->model_registry['query_object'] = 'list';
        }

        return $this;
    }

    /**
     * Set Field Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsGroup()
    {
        if (isset($this->model_registry['process_events'])) {
            $process_events = $this->model_registry['process_events'];
        } else {
            $process_events = 1;
        }

        if ($process_events === 0) {
        } else {
            $process_events = 1;
        }

        $this->model_registry['process_events'] = $process_events;

        return $this;
    }

    /**
     * Set Primary Key Defaults for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryCriteriaValues()
    {
        $this->verifyPropertyExists('criteria_status', '');
        $this->verifyPropertyExists('criteria_source_id', 0);
        $this->verifyPropertyExists('criteria_catalog_type_id', 0);
        $this->verifyPropertyExists('catalog_type_id', 0);
        $this->verifyPropertyExists('menu_id', 0);
        $this->verifyPropertyExists('criteria_extension_instance_id', 0);

        return $this;
    }

    /**
     * Set Primary Key Defaults for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsKeys()
    {
        $this->verifyPropertyExists('primary_key', 'id');
        $this->verifyPropertyExists('primary_key_value', 0);

        $key = $this->getModelRegistryPrimaryKeyValue();
        $this->setModelRegistryPrimaryKeyValue($key);

        $this->verifyPropertyExists('name_key', 'title');
        $this->verifyPropertyExists('name_key_value', null);

        return $this;
    }

    /**
     * Get the Primary Key Value for the Model Registry
     *
     * @return  integer
     * @since   1.0
     */
    protected function getModelRegistryPrimaryKeyValue()
    {
        $key = 0;

        if (isset($this->model_registry['primary_key_value'])) {
            $key = (int)$this->model_registry['primary_key_value'];
        }

        if ((int)$this->model_registry['primary_key_value'] === 0) {
            if (isset($this->model_registry['criteria_source_id'])) {
                $key = (int)$this->model_registry['criteria_source_id'];
            }
        }

        return $key;
    }

    /**
     * Get the Primary Key Value for the Model Registry
     *
     * @param integer $key
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryPrimaryKeyValue($key)
    {
        if ($key === 0) {
            $this->model_registry['primary_key_value'] = null;
        } else {
            $this->model_registry['primary_key_value'] = $key;
        }

        return $this;
    }

    /**
     * Set Field Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsFields()
    {
        return $this->setPropertyArray('fields', array());
    }

    /**
     * Set Table Name Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsTableName()
    {
        return $this->verifyPropertyExists('table_name', '#__content');
    }

    /**
     * Set Primary Prefix Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsPrimaryPrefix()
    {
        return $this->verifyPropertyExists('primary_prefix', 'a');
    }

    /**
     * Set Query Object Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsQueryObject()
    {
        if (in_array(
            strtolower($this->model_registry['query_object']),
            $this->valid_query_object_values
        )
        ) {
        } else {
            $this->model_registry['query_object'] = 'list';
        }

        return $this;
    }

    /**
     * Set Criteria Array Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsCriteriaArray()
    {
        return $this->setPropertyArray('criteria', array());
    }

    /**
     * Set Joins Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsJoins()
    {
        $this->verifyPropertyExists('use_special_joins', 0);
        $this->setPropertyArray('joins', array());

        return $this;
    }

    /**
     * Set Offset, Count and Pagination Defaults for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultLimits()
    {
        $this->verifyPropertyExists('model_offset', 0);
        $this->verifyPropertyExists('model_count', 15);
        $this->verifyPropertyExists('use_pagination', 1);

        return $this;
    }

    /**
     * Pagination Query Object is not List and Pagination is Off
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryPaginationCrossEdits()
    {
        if ($this->model_registry['query_object'] === 'list') {
        } else {
            $this->model_registry['use_pagination'] = 0;
        }

        if ($this->model_registry['use_pagination'] === 0) {
            $this->model_registry['model_offset'] = 0;
            $this->model_registry['model_count']  = 0;
        }

        return $this;
    }
}
