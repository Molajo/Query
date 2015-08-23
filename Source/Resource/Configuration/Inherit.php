<?php
/**
 * Inherit
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Inherit
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Inherit extends Fields
{
    /**
     * Extends
     *
     * @var    string
     * @since  1.0.0
     */
    protected $extends;

    /**
     * Extends
     *
     * @var    string
     * @since  1.0.0
     */
    protected $extends_model_type;

    /**
     * Extends
     *
     * @var    string
     * @since  1.0.0
     */
    protected $extends_model_name;

    /**
     * Inheritance checking and setup  <model name="XYZ" extends="ThisTable"/>
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function inheritDefinition()
    {
        if ($this->processInheritance() === false) {
            return $this;
        };

        $this->extends_model_type = '';
        $this->extends_model_name = '';

        $model_types = $this->data_object->get('valid_model_types');

        foreach ($model_types as $model_type) {

            $this->processModelType($model_type);

            if ($this->extends_model_name === '') {
            } else {
                break;
            }
        }

        if ($this->extends_model_name === '') {
            $this->extends_model_name = ucfirst(strtolower($this->extends));
            $this->extends_model_type = 'Datasource';
        }

        $inherited_model = $this->getInheritedModel();

        $this->copyInheritedModel($inherited_model);

        return $this;
    }

    /**
     * Was inheritance used?  <model name="XYZ" extends="ThisTable"/>
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processInheritance()
    {
        if (count($this->xml->attributes()) === 0) {
            return false;
        }

        foreach ($this->xml->attributes() as $key => $value) {
            if ($key === 'extends') {
                $this->extends = (string)$value;
                return true;
            }
        }

        return false;
    }

    /**
     * Match to a valid Model Type
     *
     * @param   string $model_type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processModelType($model_type)
    {
        if (ucfirst(
                strtolower(substr($this->extends, strlen($this->extends)
                        - strlen($model_type), strlen($model_type))
                )
            ) === $model_type
        ) {
            $this->extends_model_name = ucfirst(strtolower(
                    substr($this->extends,
                        0,
                        strlen($this->extends) - strlen($model_type))
                )
            );
            $this->extends_model_type = $model_type;
        }

        return $this;
    }

    /**
     * Get Inherited Model
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getInheritedModel()
    {
        $inherit_model_registry = $this->extends_model_name . $this->extends_model_type;

        if ($this->registry->exists($inherit_model_registry) === true) {
            $inherited_model = $this->registry->get($inherit_model_registry);

        } else {

            if ($this->extends_model_type === 'Datasource') {
                $get_namespace = 'Molajo//Model//Datasource//' . $this->extends_model_name . '.xml';
            } else {
                $get_namespace = 'Molajo//Resource//' . $this->extends_model_name . '//Content.xml';
            }

            $inherited_model = $this->resource->get('xml://' . $get_namespace);
        }

        return $inherited_model;
    }

    /**
     * Copy Inherited Model
     *
     * @param   array $inherited_model
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function copyInheritedModel(array $inherited_model = array())
    {
        if (count($inherited_model) === 0) {
            return $this;
        }

        foreach ($inherited_model as $key => $value) {

            if ($this->registry->exists($this->model_registry, $key) === true) {
                $this->mergeInheritedModel($key, $value);
            } else {
                $this->setModelRegistryElement($key, $value);
            }
        }

        $this->copyCustomfieldGroups($inherited_model);

        return $this;
    }

    /**
     * Copy Inherited Model
     *
     * @param   array $inherited_model
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function copyCustomfieldGroups(array $inherited_model = array())
    {
        if (isset($inherited_model['customfieldgroups'])) {
        } else {
            return $this;
        }

        $customfieldgroups = $inherited_model['customfieldgroups'];

        if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {
        } else {
            return $this;
        }

        foreach ($customfieldgroups as $group) {
            $this->custom_field_groups[] = $group;
        }

        return $this;
    }

    /**
     * Merge Inherited Model Values into existing, but only for arrays (ex. parameters, metadata, etc.)
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  array
     * @since   1.0.0
     */
    protected function mergeInheritedModel($key, $value)
    {
        $existing_value = $this->registry->get($this->model_registry, $key);

        if (is_array($existing_value)) {
            $this->mergeField($key, $value);
        }

        return $this;
    }
}
