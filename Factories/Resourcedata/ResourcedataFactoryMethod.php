<?php
/**
 * Resource Data Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Resourcedata;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethodBase;

/**
 * Resource Data Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ResourcedataFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
{
    /**
     * Valid Data Object Types
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_dataobject_types;

    /**
     * Valid Data Object Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_dataobject_attributes;

    /**
     * Valid Model Types
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_model_types;

    /**
     * Valid Model Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_model_attributes;

    /**
     * Valid Data Types
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_data_types;

    /**
     * Valid Query Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_queryelements_attributes;

    /**
     * Valid Field Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_field_attributes;

    /**
     * Valid Join Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_join_attributes;

    /**
     * Valid Foreignkey Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_foreignkey_attributes;

    /**
     * Valid Criteria Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_criteria_attributes;

    /**
     * Valid Children Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_children_attributes;

    /**
     * Valid Plugin Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_plugin_attributes;

    /**
     * Valid Value Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_value_attributes;

    /**
     * Valid Field Attribute Defaults
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_field_attributes_default;

    /**
     * Datalists
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_datalists;

    /**
     * Constructor
     *
     * @param  array $option
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['product_namespace']        = 'Molajo\\Resource\\Configuration\\Data';
        $options['store_instance_indicator'] = true;
        $options['product_name']             = basename(__DIR__);

        parent::__construct($options);

        $this->options['Resource'] = $options['Resource'];
    }

    /**
     * Retrieve and load valid properties for fields, data models and data objects
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setDependencies(array $reflection = null)
    {
        parent::setDependencies($reflection);

        $options                             = array();
        $options['product_namespace']        = 'Molajo\\Resource\\Configuration\\Registry';
        $options['store_instance_indicator'] = true;
        $this->dependencies['Registry']      = $options;

        $options = array();

        $fields = $this->options['Resource']->get('xml:///Molajo//Model//Application//Fields.xml');

        /** Data Objects */
        $this->loadFieldProperties(
            $fields,
            'dataobjecttypes',
            'dataobjecttype',
            'valid_dataobject_types'
        );
        $this->loadFieldPropertiesWithAttributes(
            $fields,
            'dataobjectattributes',
            'dataobjectattribute',
            'valid_dataobject_attributes'
        );

        /** Models */
        $this->loadFieldProperties(
            $fields,
            'modeltypes',
            'modeltype',
            'valid_model_types'
        );
        $this->loadFieldPropertiesWithAttributes(
            $fields,
            'modelattributes',
            'modelattribute',
            'valid_model_attributes'
        );

        /** Data Types */
        $this->loadFieldPropertiesWithAttributes(
            $fields,
            'datatypes',
            'datatypes',
            'valid_data_types'
        );
        $this->loadFieldProperties(
            $fields,
            'queryelements',
            'queryelement',
            'valid_queryelements_attributes'
        );

        $list = $this->valid_queryelements_attributes;

        foreach ($list as $item) {
            $field = explode(',', $item);
            $this->loadFieldProperties($fields, $field[0], $field[1], $field[2]);
        }

        $datalistsArray = array();
        $datalistsArray = $this->loadDatalists(
            $datalistsArray,
            $this->options['base_path'] . '/vendor/molajo/application/Source/Model/Datalist'
        );
        $datalistsArray = array_unique($datalistsArray);

        $this->valid_datalists = $datalistsArray;

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        try {
            $class = $this->product_namespace;

            $this->product_result = new $class(
                $this->valid_dataobject_types,
                $this->valid_dataobject_attributes,
                $this->valid_model_types,
                $this->valid_model_attributes,
                $this->valid_data_types,
                $this->valid_queryelements_attributes,
                $this->valid_field_attributes,
                $this->valid_join_attributes,
                $this->valid_foreignkey_attributes,
                $this->valid_criteria_attributes,
                $this->valid_children_attributes,
                $this->valid_plugin_attributes,
                $this->valid_value_attributes,
                $this->valid_field_attributes_default,
                $this->valid_datalists
            );
        } catch (Exception $e) {

            throw new RuntimeException
            (
                'IoC Factory Method Adapter Instance Failed for ' . $this->product_namespace
                . ' failed.' . $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * Follows the completion of the instantiate method
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onAfterInstantiation()
    {
        $dataObject = $this->createDataobject();
        $model      = $this->createModel();
        $xml        = $this->createXml($model, $dataObject);
        $this->options['Resource']->setAdapterInstance('Xml', $xml);

        return $this;
    }

    /**
     * Factory Method Controller requests any Products (other than the current product) to be saved
     *
     * @return  array
     * @since   1.0
     */
    public function setContainerEntries()
    {
        $this->set_container_entries['Resource'] = $this->options['Resource'];

        return $this->set_container_entries;
    }

    /**
     * loadFieldProperties
     *
     * @param   string $xml
     * @param   string $plural
     * @param   string $singular
     * @param   string $parameter_name
     *
     * @return  $this
     * @since   1.0
     */
    protected function loadFieldProperties($xml, $plural, $singular, $parameter_name)
    {
        if (isset($xml->$plural->$singular)) {
        } else {
            return false;
        }

        $types = $xml->$plural->$singular;

        if (count($types) === 0) {
            return false;
        }

        $typeArray = array();
        foreach ($types as $type) {
            $typeArray[] = (string)$type;
        }

        $this->$parameter_name = $typeArray;

        return $this;
    }

    /**
     * loadFieldPropertiesWithAttributes
     *
     * @param   string $xml
     * @param   string $plural
     * @param   string $singular
     * @param   string $parameter_name
     *
     * @return  $this
     * @since   1.0
     */
    protected function loadFieldPropertiesWithAttributes($xml, $plural, $singular, $parameter_name)
    {
        if (isset($xml->$plural->$singular)) {
        } else {
            return $this;
        }

        $typeArray        = array();
        $typeDefaultArray = array();
        foreach ($xml->$plural->$singular as $type) {
            $typeArray[]                             = (string)$type['name'];
            $typeDefaultArray[(string)$type['name']] = (string)$type['default'];
        }

        $this->$parameter_name = $typeArray;
        $temp                  = $parameter_name . '_defaults';
        $this->$temp           = $typeDefaultArray;

        return $this;
    }

    /**
     * loadDatalists
     *
     * @param   string $datalistsArray
     * @param   string $folder
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function loadDatalists($datalistsArray, $folder)
    {
        try {
            $dirRead = dir($folder);
            $path    = $dirRead->path;

            while (false !== ($entry = $dirRead->read())) {
                if (is_dir($path . '/' . $entry)) {
                } else {
                    $datalistsArray[] = substr($entry, 0, strlen($entry) - 4);
                }
            }

            $dirRead->close();
        } catch (RuntimeException $e) {
            throw new RuntimeException
            (
                'IoC Factory Method Configuration: loadDatalists cannot find Datalists file for folder: ' . $folder
            );
        }

        return $datalistsArray;
    }

    /**
     * Create Dataobject
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function createDataobject()
    {
        $class = 'Molajo\\Resource\\Configuration\\Dataobject';

        try {
            $handler = new $class (
                $this->product_result,
                $this->dependencies['Registry'],
                $this->options['Resource']
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                'Resource Data Factory Method createDataobject failed: '
                . $e->getMessage()
            );
        }

        return $handler;
    }

    /**
     * Create Model
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function createModel()
    {
        $class = 'Molajo\\Resource\\Configuration\\Model';

        try {
            $handler = new $class (
                $this->product_result,
                $this->dependencies['Registry'],
                $this->options['Resource']
            );

        } catch (Exception $e) {
            throw new RuntimeException(
                'Resource Data Factory Method createModel failed: '
                . $e->getMessage()
            );
        }

        return $handler;
    }

    /**
     * Create Resource  Instances that are dependent upon configuration information
     *
     * @param   object $model_handler
     * @param   object $dataobject_handler
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function createXml($model_handler, $dataobject_handler)
    {
        $scheme = $this->createScheme();

        $resource_map = $this->readFile(
            $this->options['base_path']
            . '/Bootstrap/Files/Output/ResourceMap.json'
        );

        $class = 'Molajo\\Resource\\Adapter\\Xml';

        try {
            $xml = new $class (
                $this->options['base_path'],
                $resource_map,
                array(),
                $scheme->getScheme('Xml')->include_file_extensions,
                $model_handler,
                $dataobject_handler
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                'Resource Data Factory Method createXml failed: '
                . $e->getMessage()
            );
        }

        return $xml;
    }

    /**
     * Create Scheme Instance
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function createScheme()
    {
        $class = 'Molajo\\Resource\\Scheme';

        $input = $this->options['base_path'] . '/Bootstrap/Files/Input/SchemeArray.json';

        try {
            $scheme = new $class ($input);
        } catch (Exception $e) {
            throw new RuntimeException(
                'Resource Scheme ' . $class
                . ' Exception during Instantiation: ' . $e->getMessage()
            );
        }

        return $scheme;
    }
}
