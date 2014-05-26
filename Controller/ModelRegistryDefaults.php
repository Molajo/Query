<?php
/**
 * Model Registry Defaults
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

/**
 * Model Registry Defaults
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ModelRegistryDefaults
{
    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = null;

    /**
     * List of Valid Values for Query Object
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_query_object_values = array(
        'result',
        'item',
        'list',
        'distinct'
    );

    /**
     * List of Controller Methods
     *
     * @var    array
     * @since  1.0
     */
    protected $method_array = array(
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
     * @param  array          $model_registry
     *
     * @since  1.0
     */
    public function __construct(
        array $model_registry
    ) {
        $this->model_registry = $model_registry;
    }

    /**
     * Set Default Values for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    public function setModelRegistryDefaults()
    {
        foreach ($this->method_array as $method) {
            $this->$method();
        }

        return $this->model_registry;
    }

    /**
     * Set Default Values for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    public function setModelRegistryBase()
    {
        if (is_array($this->model_registry)) {
        } else {
            $this->model_registry = array();
        }

        if (isset($this->model_registry['query_object'])) {
        } else {
            $this->model_registry['query_object'] = 'list';
        }

        return $this->model_registry;
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
        $this->setProperty('criteria_status', '');
        $this->setProperty('criteria_source_id', 0);
        $this->setProperty('criteria_catalog_type_id', 0);
        $this->setProperty('catalog_type_id', 0);
        $this->setProperty('menu_id', 0);
        $this->setProperty('criteria_extension_instance_id', 0);

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
        $this->setProperty('primary_key', 'id');
        $this->setProperty('primary_key_value', 0);

        $key = $this->getModelRegistryPrimaryKeyValue();
        $this->setModelRegistryPrimaryKeyValue($key);

        $this->setProperty('name_key', 'title');
        $this->setProperty('name_key_value', null);

        return $this;
    }

    /**
     * Get the Primary Key Value for the Model Registry
     *
     * @return  $this
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
        return $this->setProperty('table_name', '#__content');
    }

    /**
     * Set Primary Prefix Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsPrimaryPrefix()
    {
        return $this->setProperty('primary_prefix', 'a');
    }

    /**
     * Set Query Object Default for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryDefaultsQueryObject()
    {
        if (in_array(strtolower($this->model_registry['query_object']), $this->valid_query_object_values)) {
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
        $this->setProperty('use_special_joins', 0);
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
        $this->setProperty('model_offset', 0);
        $this->setProperty('model_count', 15);
        $this->setProperty('use_pagination', 1);

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

    /**
     * Set Property
     *
     * @param   string $property
     * @param   mixed $default
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPropertyArray($property, $default = array())
    {
        $this->setProperty($property, $default);

        if (is_array($this->model_registry[$property])) {
        } else {
            $this->model_registry[$property] = array();
        }

        return $this;
    }

    /**
     * Set Property
     *
     * @param   string $property
     * @param   mixed  $default
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setProperty($property, $value = null)
    {
        $this->model_registry[$property] = $value;

        return $this;
    }
}
