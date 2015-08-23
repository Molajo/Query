<?php
/**
 * Foreignkeys
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

/**
 * Foreignkeys
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Foreignkeys extends Children
{
    /**
     * Foreignkey Model Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $foreignkey_model_type;

    /**
     * Foreignkey Model Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $foreignkey_model_name;

    /**
     * Foreignkey Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $foreignkey_registry;

    /**
     * Foreignkey Array
     *
     * @var    array
     * @since  1.0.0
     */
    protected $foreignkey_array;

    /**
     * Foreignkey Select Array
     *
     * @var    array
     * @since  1.0.0
     */
    protected $foreignkey_select_array;

    /**
     * Process all fields, filtering with valid attributes array
     *
     * @param   array $valid_attributes
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setForeignkeysRegistry(array $valid_attributes = array())
    {
        if ($this->processForeignkeys() === false) {
            return $this;
        }

        $verified_attributes = array();

        $foreignkeys = $this->getForeignkeys();

        foreach ($foreignkeys as $foreignkey) {
            $verified_attributes[] = $this->verifyAttributes($foreignkey, $valid_attributes);
        }

        $this->setModelRegistryElement('Foreignkeys', $verified_attributes);

        return $this;
    }

    /**
     * Process Foreignkeys
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processForeignkeys()
    {
        if (isset($this->xml->foreignkeys->foreignkey)) {
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
    protected function getForeignkeys()
    {
        $foreignkeys = array();

        foreach ($this->xml->table->foreignkeys->foreignkey as $item) {
            $attributes      = get_object_vars($item);
            $item_attributes = ($attributes["@attributes"]);
            foreach ($item_attributes as $key => $value) {
                $foreignkeys[] = (string)$value;
            }
        }

        return $foreignkeys;
    }

    /**
     * Process foreignkey field definitions for registry
     *
     * @param   array $model_foreignkey_array
     *
     * @return  array
     * @since   1.0.0
     */
    protected function processForeignkey(array $model_foreignkey_array = array())
    {
        $this->initialiseForeignkeyFields($model_foreignkey_array);

        $this->foreignkey_registry = $this->registry->get(
            $this->foreignkey_model_type,
            $this->foreignkey_model_name
        );

        $this->setForeignkeyArray($model_foreignkey_array);
        $this->setForeignkeyFieldsArray($model_foreignkey_array);
        $this->setForeignkeySelectFieldsArray($model_foreignkey_array);

        $this->setModelRegistryElement('Foreignkeys', $this->foreignkey_array);
        $this->setModelRegistryElement('ForeignkeyFields', $this->foreignkey_select_array);

        return $this;
    }

    /**
     * Initialise Foreignkey Fields
     *
     * @param   array $model_foreignkey_array
     *
     * @return  array
     * @since   1.0.0
     */
    protected function initialiseForeignkeyFields(array $model_foreignkey_array = array())
    {
        $this->foreignkey_model_type   = 'Datasource';
        $this->foreignkey_model_name   = ucfirst(strtolower($model_foreignkey_array['model']));
        $this->foreignkey_registry     = array();
        $this->foreignkey_array        = array();
        $this->foreignkey_select_array = array();

        return $this;
    }

    /**
     * Initialise Foreignkey Fields
     *
     * @param   array $model_foreignkey_array
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setForeignkeyArray(array $model_foreignkey_array = array())
    {
        $this->foreignkey_array['table_name'] = $this->foreignkey_registry['table_name'];

        $alias = $model_foreignkey_array['alias'];
        if (trim($alias) === '') {
            $alias = $this->foreignkey_registry['alias'];
        }
        $this->foreignkey_array['alias'] = trim($alias);

        $this->foreignkey_array['select'] = $model_foreignkey_array['select'];

        return $this;
    }

    /**
     * Set Foreignkey to and Foreignkey from in Foreignkey Array
     *
     * @param   array $model_foreignkey_array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setForeignkeyFieldsArray(array $model_foreignkey_array = array())
    {
        $this->foreignkey_array['foreignkey_to']   = $model_foreignkey_array['foreignkey_to'];
        $this->foreignkey_array['foreignkey_with'] = $model_foreignkey_array['foreignkey_with'];

        return $this;
    }

    /**
     * Set Foreignkey Select Fields Array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setForeignkeySelectFieldsArray()
    {
        $select_array = explode(',', $this->foreignkey_array['select']);

        if ((int)count($select_array) === 0) {
            return $this;
        }

        foreach ($select_array as $s) {

            foreach ($this->foreignkey_registry['Fields'] as $field) {

                if ($field['name'] === $s) {
                    $select_field               = array();
                    $select_field['name']       = trim($s);
                    $select_field['as_name']    = trim($this->foreignkey_array['alias'] . '_' . trim($s));
                    $select_field['alias']      = $this->foreignkey_array['alias'];
                    $select_field['table_name'] = $this->foreignkey_array['table_name'];

                    $this->foreignkey_select_array[$s] = $select_field;
                }
            }
        }

        return $this;
    }
}
