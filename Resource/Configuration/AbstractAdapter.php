<?php
/**
 * Abstract Adapter for XML Configuration
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Resource\Configuration;

use CommonApi\Exception\RuntimeException;
use CommonApi\Resource\ResourceInterface;
use Molajo\Resource\Api\ConfigurationDataInterface;
use Molajo\Resource\Api\ConfigurationInterface;
use Molajo\Resource\Api\RegistryInterface;
use stdClass;

/**
 * Abstract Adapter for XML Configuration
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class AbstractAdapter implements ConfigurationInterface
{
    /**
     * Data Object Instance
     *
     * @var    object Molajo\Resource\Api\ConfigurationDataInterface
     * @since  1.0
     */
    protected $dataobject;

    /**
     * Registry
     *
     * @var    object Molajo\Resource\Api\Configuration\RegistryInterface
     * @since  1.0
     */
    protected $registry;

    /**
     * Resource Instance
     *
     * @var    object Molajo\Resource\Api\ConfigurationDataInterface
     * @since  1.0
     */
    protected $resource;

    /**
     * Constructor
     *
     * @param ConfigurationDataInterface $dataobject
     * @param RegistryInterface          $registry
     * @param ResourceInterface          $resource
     *
     * @since  1.0
     */
    public function __construct(
        ConfigurationDataInterface $dataobject,
        RegistryInterface $registry,
        ResourceInterface $resource
    ) {
        $this->dataobject = $dataobject;
        $this->registry   = $registry;
        $this->resource   = $resource;
    }

    /**
     * Load registry for requested model resource, returning name of registry collection
     *
     * @param   string $model_type
     * @param   string $model_name
     * @param   object $xml
     *
     * @return  string  Name of registry model
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getConfiguration($model_type, $model_name, $xml)
    {
        throw new RuntimeException('Configuration Xml Abstract Adapter - use subclass getConfiguration');
    }

    /**
     * Store Configuration Data in Registry
     *
     * @param   string $model_registry
     * @param   object $xml
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setModelRegistry($model_registry, $xml)
    {
        throw new RuntimeException('Configuration Xml Abstract Adapter - use subclass setModelRegistry');
    }

    /**
     * Parse xml recursively, processing all include statements
     *
     * @param   string $xml
     *
     * @return  \SimpleXMLElement
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getIncludeCode($xml)
    {
        $pre_string = $xml->asXML();

        $done = false;

        while ($done === false) {
            $post_string = $this->getIncludeCodeLoop($pre_string);
            if ($post_string === $pre_string) {
                $done = true;
            } else {
                $pre_string = $post_string;
            }
        }

        return simplexml_load_string($post_string);
    }

    /**
     * Parse xml recursively, processing all include statements
     *
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getIncludeCodeLoop($xml_string)
    {
        $pattern = '/<include (.*)="(.*)"\/>/';

        preg_match_all($pattern, $xml_string, $matches);

        $replace_this_array = $matches[0];
        $type_array         = $matches[1];
        $include_name_array = $matches[2];

        if (count($replace_this_array) === 0) {
            return $xml_string;
        }

        for ($i = 0; $i < count($replace_this_array); $i++) {

            $replace_this = $replace_this_array[$i];
            $name         = $include_name_array[$i];

            if (trim(strtolower($type_array[$i])) === 'field') {
                $model_name = 'xml:///Molajo//Model//Fields//' . $name . '.xml';
                $with_this  = $this->resource->get($model_name);
            } else {
                $model_name = 'xml:///Molajo//Model//Include//' . $name . '.xml';
                $with_this  = $this->resource->get($model_name);
            }

            $xml_string = str_replace($replace_this, $with_this, $xml_string);
        }

        return $xml_string;
    }


    /**
     * Define elements for Data Model to Registry
     *
     * @param   string $model_registry
     * @param   object $xml
     * @param   string $plural
     * @param   string $singular
     * @param   string $valid_attributes
     *
     * @return  bool
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setElementsRegistry($model_registry, $xml, $plural, $singular, $valid_attributes)
    {
        if (isset($xml->table->$plural->$singular)) {
        } else {
            return true;
        }

        $set = $xml->table->$plural;

        $itemArray = array();

        if (count($set->$singular) > 0) {

            foreach ($set->$singular as $item) {

                $attributes = get_object_vars($item);

                $itemAttributes      = ($attributes["@attributes"]);
                $itemAttributesArray = array();

                if (count($itemAttributes) > 0) {

                    foreach ($itemAttributes as $key => $value) {

                        if (in_array($key, $valid_attributes)) {
                        } else {
                            throw new RuntimeException(
                                'Configuration: setElementsRegistry encountered Invalid Model Attribute '
                                . $key . ' for ' . $model_registry
                            );
                        }

                        $itemAttributesArray[$key] = $value;
                    }
                }

                if ($plural === 'plugins') {
                    if (count($itemAttributesArray) > 0) {
                        foreach ($itemAttributesArray as $plugin) {
                            $itemArray[] = $plugin;
                        }
                    }
                } else {
                    $itemArray[] = $itemAttributesArray;
                }
            }
        }

        if ($plural === 'joins') {
            $joins   = array();
            $selects = array();

            for ($i = 0; $i < count($itemArray); $i++) {
                $temp      = $this->setJoinFields($itemArray[$i]);
                $joins[]   = $temp[0];
                $selects[] = $temp[1];
            }

            $this->registry->set($model_registry, $plural, $joins);

            $this->registry->set($model_registry, 'JoinFields', $selects);

        } elseif ($plural === 'values') {

            $valuesArray = array();

            if (count($itemArray) > 0) {

                foreach ($itemArray as $value) {

                    if (is_array($value)) {
                        $temp_row = $value;
                    } else {
                        $valueVars = get_object_vars($value);
                        $temp_row  = ($valueVars["@attributes"]);
                    }

                    $temp = new stdClass();

                    $temp->id    = $temp_row['id'];
                    $temp->value = $temp_row['value'];

                    $valuesArray[] = $temp;
                }

                $this->registry->set($model_registry, 'values', $valuesArray);
            }
        } else {
            $this->registry->set($model_registry, $plural, $itemArray);
        }

        return true;
    }

    /**
     * Process join field definitions for registry
     *
     * @param   array $modelJoinArray
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setJoinFields($modelJoinArray)
    {
        $joinArray       = array();
        $joinSelectArray = array();

        $joinModel    = ucfirst(strtolower($modelJoinArray['model']));
        $joinRegistry = $joinModel . 'Datasource';

        if ($this->registry->exists($joinRegistry) === false) {
            ;
            $this->resource->get('xml:///Molajo//Model//Datasource//' . $joinModel . '.xml');
        }

        $fields = $this->registry->get($joinRegistry, 'Fields');

        $table = $this->registry->get($joinRegistry, 'table_name');

        $joinArray['table_name'] = $table;

        $alias = (string)$modelJoinArray['alias'];
        if (trim($alias) === '') {
            $alias = substr($table, 3, strlen($table));
        }
        $joinArray['alias'] = trim($alias);

        $select              = (string)$modelJoinArray['select'];
        $joinArray['select'] = $select;

        $selectArray = explode(',', $select);

        if ((int)count($selectArray) > 0) {

            foreach ($selectArray as $s) {

                foreach ($fields as $joinSelectArray) {
                    if ($joinSelectArray['name'] === $s) {
                        $joinSelectArray['as_name']    = trim($alias) . '_' . trim($s);
                        $joinSelectArray['alias']      = $alias;
                        $joinSelectArray['table_name'] = $table;
                    }
                }
            }
        }

        $joinArray['jointo']   = (string)$modelJoinArray['jointo'];
        $joinArray['joinwith'] = (string)$modelJoinArray['joinwith'];

        return array($joinArray, $joinSelectArray);
    }

    /**
     * getCustomFields extracts field information for all customfield groups
     *
     * @param   string $model_registry
     * @param   object $xml
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getCustomFields($model_registry, $xml)
    {
        $customFieldsArray = array();

        if (count($xml->customfields->customfield) > 0) {

            foreach ($xml->customfields->customfield as $custom_field) {

                $name    = (string)$custom_field['name'];
                $results = $this->getCustomFieldsSpecificGroup($model_registry, $custom_field);
                if ($results === false) {
                } else {

                    $fieldArray = $results[0];
                    $fieldNames = $results[1];

                    $this->inheritCustomFieldsSpecificGroup(
                        $model_registry,
                        $name,
                        $fieldArray,
                        $fieldNames
                    );

                    $customFieldsArray[] = $name;
                }
            }
        }

        /** Include Inherited Groups not matching existing groups */
        $exists = $this->registry->exists($model_registry, 'Customfieldgroups');

        if ($exists === true) {
            $inherited = $this->registry->get($model_registry, 'Customfieldgroups');

            if (is_array($inherited) && count($inherited) > 0) {
                foreach ($inherited as $name) {

                    if (in_array($name, $customFieldsArray)) {
                    } else {
                        $results = $this->inheritCustomFieldsSpecificGroup($model_registry, $name);
                        if ($results === false) {
                        } else {
                            $customFieldsArray[] = $name;
                        }
                    }
                }
            }
        }

        $this->registry->set($model_registry, 'Customfieldgroups', array_unique($customFieldsArray));

        return;
    }

    /**
     * Load Custom Fields for a specific Group -- this is called once for each custom field type for a Model
     *
     * @param   string $model_registry
     * @param          $customfield
     *
     * @return  array|bool
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getCustomFieldsSpecificGroup($model_registry, $customfield)
    {
        $fieldArray = array();
        $fieldNames = array();

        if (count($customfield) > 0) {

            foreach ($customfield as $key1 => $value1) {

                $attributes           = get_object_vars($value1);
                $fieldAttributes      = ($attributes["@attributes"]);
                $fieldAttributesArray = array();

                if (count($fieldAttributes) > 0) {
                    foreach ($fieldAttributes as $key2 => $value2) {

                        if ($key2 === 'fieldset') {
                        } elseif (in_array($key2, $this->dataobject->get('valid_field_attributes'))) {
                        } else {
                            throw new RuntimeException(
                                'Configuration: getCustomFieldsSpecificGroup Invalid Field attribute '
                                . $key2 . ':' . $value2 . ' for ' . $model_registry
                            );
                        }

                        if ($key2 === 'name') {
                        } else {
                            $fieldNames[] = $value2;
                        }

                        $fieldAttributesArray[$key2] = $value2;
                    }
                }

                $fieldAttributesArray['field_inherited'] = 0;

                $fieldArray[] = $fieldAttributesArray;
            }
        }

        if (is_array($fieldArray) && count($fieldArray) > 0) {
        } else {
            return false;
        }

        return array($fieldArray, $fieldNames);
    }

    /**
     * Inherited fields are merged in with those specifically defined in model
     *
     * @param   string $model_registry
     * @param          $name
     * @param          $fieldArray
     * @param          $fieldNames
     *
     * @return array
     * @since   1.0
     */
    protected function inheritCustomFieldsSpecificGroup(
        $model_registry,
        $name,
        $fieldArray = array(),
        $fieldNames = array()
    ) {
        $available = $this->registry->get($model_registry, $name, array());

        if (count($available) > 0) {

            foreach ($available as $temp_row) {

                foreach ($temp_row as $field => $fieldvalue) {

                    if ($field === 'name') {

                        if (in_array($fieldvalue, $fieldNames)) {
                        } else {
                            $temp_row['field_inherited'] = 1;
                            $fieldArray[]                = $temp_row;
                            $fieldNames[]                = $fieldvalue;
                        }
                    }
                }
            }
        }

        if (is_array($fieldArray) && count($fieldArray) === 0) {
            $this->registry->set($model_registry, $name, array());

            return false;
        }

        $this->registry->set($model_registry, $name, $fieldArray);

        return $name;
    }

    /**
     * Inheritance checking and setup  <model name="XYZ" extends="ThisTable"/>
     *
     * @param   string $model_registry
     * @param          $xml
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function inheritDefinition($model_registry, $xml)
    {
//echo 'MODELREGISTRY ' . $model_registry . '<br />';
//echo '<pre>';
//var_dump($xml);

        $extends = false;

        if (count($xml->attributes()) > 0) {
            foreach ($xml->attributes() as $key => $value) {
                if ($key === 'extends') {
                    $extends = (string)$value;
                }
            }
        }
        if ($extends === false) {
            return $this;
        }

        $modelArray = $this->dataobject->get('valid_model_types');

        $extends_model_name = '';
        $extends_model_type = '';
        if (count($modelArray) > 0) {

            foreach ($modelArray as $modeltype) {
                if (ucfirst(
                        strtolower(substr($extends, strlen($extends) - strlen($modeltype), strlen($modeltype)))
                    ) === $modeltype
                ) {
                    $extends_model_name = ucfirst(
                        strtolower(substr($extends, 0, strlen($extends) - strlen($modeltype)))
                    );
                    $extends_model_type = $modeltype;
                    break;
                }
            }
        }

        if ($extends_model_name === '') {
            $extends_model_name = ucfirst(strtolower($extends));
            $extends_model_type = 'Datasource';
        }

        $inheritModelRegistry = $extends_model_name . $extends_model_type;

        if ($this->registry->exists($inheritModelRegistry) === true) {
        } else {
            if ($extends_model_type === 'Datasource') {
                $this->resource->get('xml:///Molajo//Model//Datasource//' . $extends_model_name . '.xml');
            } else {
                $this->resource->get('xml:///Molajo//' . $extends_model_name . '//Configuration.xml');
            }
        }

        $this->registry->copy($inheritModelRegistry, $model_registry);

        return $this;
    }
}
