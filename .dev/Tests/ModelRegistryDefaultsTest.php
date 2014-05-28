<?php
/**
 * ModelRegistryDefaults Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Controller\ControllerInterface;
use CommonApi\Database\DatabaseInterface;
use CommonApi\Model\ModelInterface;
use CommonApi\Query\QueryInterface;

use Molajo\Controller\ModelRegistryDefaults;

use PHPUnit_Framework_TestCase;

/**
 * ModelRegistryDefaults
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ModelRegistryDefaultsTest extends PHPUnit_Framework_TestCase
{
    protected $controller;

    /**
     * Test Empty Model Regitsry - defaults
     *
     * @covers  Molajo\Controller\ModelRegistryDefaults::__construct
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryBase
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistryDefaults::getModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::verifyPropertyExists
     * @covers  Molajo\Controller\ModelRegistryDefaults::setProperty
     *
     * @return void
     * @since   1.0
     */
    public function testSetModelRegistryDefaults()
    {
        $model_registry = array();
        $this->controller = new MockModelRegistryDefaults($model_registry);

        $model_registry = $this->controller->setModelRegistryDefaults();

        $this->assertEquals('list', $model_registry['query_object']);
        $this->assertEquals(1, $model_registry['process_events']);
        $this->assertEquals('', $model_registry['criteria_status']);
        $this->assertEquals(0, $model_registry['criteria_source_id']);
        $this->assertEquals(0, $model_registry['criteria_catalog_type_id']);
        $this->assertEquals(0, $model_registry['catalog_type_id']);
        $this->assertEquals(0, $model_registry['menu_id']);
        $this->assertEquals(0, $model_registry['criteria_extension_instance_id']);
        $this->assertEquals('id', $model_registry['primary_key']);
        $this->assertEquals('', $model_registry['primary_key_value']);
        $this->assertEquals('title', $model_registry['name_key']);
        $this->assertEquals('', $model_registry['name_key_value']);
        $this->assertTrue(is_array($model_registry['fields']));
        $this->assertEquals('#__content', $model_registry['table_name']);
        $this->assertEquals('a', $model_registry['primary_prefix']);
        $this->assertTrue(is_array($model_registry['criteria']));
        $this->assertEquals(0, $model_registry['use_special_joins']);
        $this->assertTrue(is_array($model_registry['joins']));
        $this->assertEquals(0, $model_registry['model_offset']);
        $this->assertEquals(15, $model_registry['model_count']);
        $this->assertEquals(1, $model_registry['use_pagination']);

        return;
    }

    /**
     * Test with Catalog Model Registry
     *
     * @covers  Molajo\Controller\ModelRegistryDefaults::__construct
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryBase
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistryDefaults::getModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::verifyPropertyExists
     * @covers  Molajo\Controller\ModelRegistryDefaults::setProperty
     *
     * @return void
     * @since   1.0
     */
    public function testCatalogModelRegistry()
    {
        $mock = new MockModelRegistry();
        $model_registry = $mock->create();
        $this->controller = new MockModelRegistryDefaults($model_registry);
        $model_registry = $this->controller->setModelRegistryDefaults();

        $this->assertEquals('Catalog', $model_registry['name']);
        $this->assertEquals('#__catalog', $model_registry['table_name']);
        $this->assertEquals('id', $model_registry['primary_key']);
        $this->assertEquals('a', $model_registry['primary_prefix']);
        $this->assertEquals(0, $model_registry['get_customfields']);
        $this->assertEquals(0, $model_registry['get_item_children']);
        $this->assertEquals(0, $model_registry['use_special_joins']);
        $this->assertEquals(0, $model_registry['check_view_level_access']);
        $this->assertEquals(0, $model_registry['process_events']);
        $this->assertEquals(1, $model_registry['use_pagination']);
        $this->assertEquals('Database', $model_registry['data_object']);
        $this->assertEquals('Catalog', $model_registry['model_name']);
        $this->assertEquals('Datasource', $model_registry['model_type']);
        $this->assertEquals('CatalogDatasource', $model_registry['model_registry_name']);
        $this->assertEquals('Dataobject', $model_registry['data_object_data_object']);
        $this->assertEquals('Database', $model_registry['data_object_data_object_type']);
        $this->assertEquals('molajo', $model_registry['data_object_db']);
        $this->assertEquals('localhost', $model_registry['data_object_db_host']);
        $this->assertEquals('', $model_registry['data_object_db_password']);
        $this->assertEquals('molajo_', $model_registry['data_object_db_prefix']);
        $this->assertEquals('mysqli', $model_registry['data_object_db_type']);
        $this->assertEquals('', $model_registry['data_object_db_user']);
        $this->assertEquals('Database', $model_registry['data_object_model_name']);
        $this->assertEquals('Dataobject', $model_registry['data_object_model_type']);
        $this->assertEquals('Database', $model_registry['data_object_name']);
        $this->assertEquals(1, $model_registry['data_object_process_events']);
        $this->assertTrue(is_array($model_registry['fields']));
        $this->assertTrue(is_array($model_registry['joins']));
        $this->assertTrue(is_array($model_registry['joinfields']));
        $this->assertTrue(is_array($model_registry['foreignkeys']));
        $this->assertTrue(is_array($model_registry['criteria']));
        $this->assertTrue(is_array($model_registry['children']));
        $this->assertTrue(is_array($model_registry['customfieldgroups']));
        $this->assertEquals(0, $model_registry['model_offset']);
        $this->assertEquals(20, $model_registry['model_count']);
        $this->assertTrue(is_array($model_registry['fields']['0']));
        $this->assertTrue(is_array($model_registry['fields']['1']));
        $this->assertTrue(is_array($model_registry['fields']['2']));
        $this->assertTrue(is_array($model_registry['fields']['3']));
        $this->assertTrue(is_array($model_registry['fields']['4']));
        $this->assertTrue(is_array($model_registry['fields']['5']));
        $this->assertTrue(is_array($model_registry['fields']['6']));
        $this->assertTrue(is_array($model_registry['fields']['7']));
        $this->assertTrue(is_array($model_registry['fields']['8']));
        $this->assertTrue(is_array($model_registry['fields']['9']));
        $this->assertTrue(is_array($model_registry['fields']['10']));
        $this->assertEquals('id', $model_registry['fields']['0']['name']);
        $this->assertEquals('integer', $model_registry['fields']['0']['type']);
        $this->assertEquals(1, $model_registry['fields']['0']['null']);
        $this->assertEquals(0, $model_registry['fields']['0']['default']);
        $this->assertEquals(1, $model_registry['fields']['0']['identity']);
        $this->assertEquals('application_id', $model_registry['fields']['1']['name']);
        $this->assertEquals('integer', $model_registry['fields']['1']['type']);
        $this->assertEquals(0, $model_registry['fields']['1']['null']);
        $this->assertEquals('', $model_registry['fields']['1']['default']);
        $this->assertEquals('catalog_type_id', $model_registry['fields']['2']['name']);
        $this->assertEquals('integer', $model_registry['fields']['2']['type']);
        $this->assertEquals(0, $model_registry['fields']['2']['null']);
        $this->assertEquals('model', $model_registry['fields']['2']['default']);
        $this->assertEquals('CatalogType', $model_registry['fields']['2']['datalist']);
        $this->assertEquals(0, $model_registry['fields']['2']['locked']);
        $this->assertEquals('catalog_types_title', $model_registry['fields']['2']['display']);
        $this->assertEquals('source_id', $model_registry['fields']['3']['name']);
        $this->assertEquals('integer', $model_registry['fields']['3']['type']);
        $this->assertEquals(0, $model_registry['fields']['3']['null']);
        $this->assertEquals(' ', $model_registry['fields']['3']['default']);
        $this->assertEquals(1, $model_registry['fields']['3']['hidden']);
        $this->assertEquals('enabled', $model_registry['fields']['4']['name']);
        $this->assertEquals('boolean', $model_registry['fields']['4']['type']);
        $this->assertEquals(0, $model_registry['fields']['4']['null']);
        $this->assertEquals(0, $model_registry['fields']['4']['default']);
        $this->assertEquals('redirect_to_id', $model_registry['fields']['5']['name']);
        $this->assertEquals('integer', $model_registry['fields']['5']['type']);
        $this->assertEquals(0, $model_registry['fields']['5']['null']);
        $this->assertEquals(0, $model_registry['fields']['5']['default']);
        $this->assertEquals('sef_request', $model_registry['fields']['6']['name']);
        $this->assertEquals('string', $model_registry['fields']['6']['type']);
        $this->assertEquals(0, $model_registry['fields']['6']['null']);
        $this->assertEquals(' ', $model_registry['fields']['6']['default']);
        $this->assertEquals('page_type', $model_registry['fields']['7']['name']);
        $this->assertEquals('string', $model_registry['fields']['7']['type']);
        $this->assertEquals(0, $model_registry['fields']['7']['null']);
        $this->assertEquals(' ', $model_registry['fields']['7']['default']);
        $this->assertEquals('Pagetypes', $model_registry['fields']['7']['datalist']);
        $this->assertEquals('extension_instance_id', $model_registry['fields']['8']['name']);
        $this->assertEquals('integer', $model_registry['fields']['8']['type']);
        $this->assertEquals(0, $model_registry['fields']['8']['null']);
        $this->assertEquals(0, $model_registry['fields']['8']['default']);
        $this->assertEquals('ExtensionInstances', $model_registry['fields']['8']['datalist']);
        $this->assertEquals(1, $model_registry['fields']['8']['locked']);
        $this->assertEquals('name', $model_registry['fields']['8']['display']);
        $this->assertEquals('view_group_id', $model_registry['fields']['9']['name']);
        $this->assertEquals('integer', $model_registry['fields']['9']['type']);
        $this->assertEquals(0, $model_registry['fields']['9']['null']);
        $this->assertEquals(0, $model_registry['fields']['9']['default']);
        $this->assertEquals('primary_category_id', $model_registry['fields']['10']['name']);
        $this->assertEquals('integer', $model_registry['fields']['10']['type']);
        $this->assertEquals(0, $model_registry['fields']['10']['null']);
        $this->assertEquals(0, $model_registry['fields']['10']['default']);
        $this->assertTrue(is_array($model_registry['joins']['0']));
        $this->assertTrue(is_array($model_registry['joins']['1']));
        $this->assertTrue(is_array($model_registry['joins']['2']));
        $this->assertEquals('#__catalog_types', $model_registry['joins']['0']['table_name']);
        $this->assertEquals('b', $model_registry['joins']['0']['alias']);
        $this->assertEquals('title,model_type,model_name,primary_category_id,alias', $model_registry['joins']['0']['select']);
        $this->assertEquals('id', $model_registry['joins']['0']['jointo']);
        $this->assertEquals('catalog_type_id', $model_registry['joins']['0']['joinwith']);
        $this->assertEquals('#__application_extension_instances', $model_registry['joins']['1']['table_name']);
        $this->assertEquals('application_extension_instances', $model_registry['joins']['1']['alias']);
        $this->assertEquals('', $model_registry['joins']['1']['select']);
        $this->assertEquals('application_id,extension_instance_id', $model_registry['joins']['1']['jointo']);
        $this->assertEquals('APPLICATION_ID,extension_instance_id', $model_registry['joins']['1']['joinwith']);
        $this->assertEquals('#__site_extension_instances', $model_registry['joins']['2']['table_name']);
        $this->assertEquals('site_extension_instances', $model_registry['joins']['2']['alias']);
        $this->assertEquals('', $model_registry['joins']['2']['select']);
        $this->assertEquals('site_id,extension_instance_id', $model_registry['joins']['2']['jointo']);
        $this->assertEquals('SITE_ID,extension_instance_id', $model_registry['joins']['2']['joinwith']);
        $this->assertTrue(is_array($model_registry['joinfields']['0']));
        $this->assertTrue(is_array($model_registry['joinfields']['1']));
        $this->assertTrue(is_array($model_registry['joinfields']['2']));
        $this->assertEquals('protected', $model_registry['joinfields']['0']['name']);
        $this->assertEquals('boolean', $model_registry['joinfields']['0']['type']);
        $this->assertEquals(0, $model_registry['joinfields']['0']['null']);
        $this->assertEquals(0, $model_registry['joinfields']['0']['default']);
        $this->assertEquals(1, $model_registry['joinfields']['0']['locked']);
        $this->assertEquals(1, $model_registry['joinfields']['0']['hidden']);
        $this->assertEquals('extension_instance_id', $model_registry['joinfields']['1']['name']);
        $this->assertEquals('integer', $model_registry['joinfields']['1']['type']);
        $this->assertEquals(0, $model_registry['joinfields']['1']['null']);
        $this->assertEquals(0, $model_registry['joinfields']['1']['default']);
        $this->assertEquals('ExtensionInstances', $model_registry['joinfields']['1']['datalist']);
        $this->assertEquals(1, $model_registry['joinfields']['1']['locked']);
        $this->assertEquals('name', $model_registry['joinfields']['1']['display']);
        $this->assertEquals('extension_instance_id', $model_registry['joinfields']['2']['name']);
        $this->assertEquals('integer', $model_registry['joinfields']['2']['type']);
        $this->assertEquals(0, $model_registry['joinfields']['2']['null']);
        $this->assertEquals(0, $model_registry['joinfields']['2']['default']);
        $this->assertEquals('ExtensionInstances', $model_registry['joinfields']['2']['datalist']);
        $this->assertEquals(1, $model_registry['joinfields']['2']['locked']);
        $this->assertEquals('name', $model_registry['joinfields']['2']['display']);
        $this->assertTrue(is_array($model_registry['foreignkeys']['0']));
        $this->assertEquals('catalog_type_id', $model_registry['foreignkeys']['0']['name']);
        $this->assertEquals('id', $model_registry['foreignkeys']['0']['source_id']);
        $this->assertEquals('CatalogTypes', $model_registry['foreignkeys']['0']['source_model']);
        $this->assertEquals(1, $model_registry['foreignkeys']['0']['required']);
        $this->assertTrue(is_array($model_registry['criteria']['0']));
        $this->assertTrue(is_array($model_registry['criteria']['1']));
        $this->assertEquals('a.enabled', $model_registry['criteria']['0']['name']);
        $this->assertEquals('=', $model_registry['criteria']['0']['connector']);
        $this->assertEquals(1, $model_registry['criteria']['0']['value']);
        $this->assertEquals('a.redirect_to_id', $model_registry['criteria']['1']['name']);
        $this->assertEquals('=', $model_registry['criteria']['1']['connector']);
        $this->assertEquals(0, $model_registry['criteria']['1']['value']);
        $this->assertTrue(is_array($model_registry['children']['0']));
        $this->assertTrue(is_array($model_registry['children']['1']));
        $this->assertEquals('Catalogactivity', $model_registry['children']['0']['name']);
        $this->assertEquals('Datasource', $model_registry['children']['0']['type']);
        $this->assertEquals('catalog_id', $model_registry['children']['0']['join']);
        $this->assertEquals('Catalogcategories', $model_registry['children']['1']['name']);
        $this->assertEquals('Datasource', $model_registry['children']['1']['type']);
        $this->assertEquals('catalog_id', $model_registry['children']['1']['join']);

        return;
    }
}

/**
 * Mock Listener Classes
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class MockModelRegistryDefaults extends ModelRegistryDefaults
{
    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    public $model_registry = null;
}

/**
 * Class MockModelRegistry
 *
 * Generates a registry for testing when used with the web and ModelRegistryDefaults
 *
 * @package Molajo\Controller
 */
class MockModelRegistry
{
    protected $model_registry;

    /**
     * load a model_registry into ModelRegistryDefaults
     *  run from setModelRegistryDefaults fire up a browser, copy results into create
     */

    /**
     * Set Default Values for Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    public function setModelRegistryDefaults()
    {
     //   foreach ($this->method_array as $method) {
     //       $this->$method();
     //   }
//        $this->generate();

//        return $this->model_registry;
    }
    public function generate()
    {
        $field1_arrays = $this->echoModelRegistryEntries($this->model_registry);
        foreach ($field1_arrays as $field1_key) {
            $field2_arrays = $this->echoModelRegistryEntries($this->model_registry[$field1_key], $field1_key);
            foreach ($field2_arrays as $field2_key) {
                $this->echoModelRegistryEntries($this->model_registry[$field1_key][$field2_key], $field1_key, $field2_key);
            }
        }
    }

    public function echoModelRegistryEntries($model_registry, $field1 = null, $field2 = null)
    {
        $more_arrays = array();

        foreach ($model_registry as $key => $value) {

            if (is_numeric($value)) {

            } elseif (is_array($value)) {
                $value = "array()";
                $more_arrays[] = $key;

            } else {
                $value = "'" . $value . "'";
            }

            $use_field = '';

            if ($field1 === null) {
            } else {
                $use_field .=  "['" . $field1 . "']";
            }

            if ($field2 === null) {
            } else {
                $use_field .=  "['" . $field2 . "']";
            }

            $use_field .= "['" . $key . "']";

            echo '$model_registry' . $use_field . ' = ' . $value . ';' . '<br>';
        }

        return $more_arrays;
    }

    public function create()
    {
        $model_registry['name'] = 'Catalog';
        $model_registry['table_name'] = '#__catalog';
        $model_registry['primary_key'] = 'id';
        $model_registry['primary_prefix'] = 'a';
        $model_registry['get_customfields'] = 0;
        $model_registry['get_item_children'] = 0;
        $model_registry['use_special_joins'] = 0;
        $model_registry['check_view_level_access'] = 0;
        $model_registry['process_events'] = 0;
        $model_registry['use_pagination'] = 1;
        $model_registry['data_object'] = 'Database';
        $model_registry['model_name'] = 'Catalog';
        $model_registry['model_type'] = 'Datasource';
        $model_registry['model_registry_name'] = 'CatalogDatasource';
        $model_registry['data_object_data_object'] = 'Dataobject';
        $model_registry['data_object_data_object_type'] = 'Database';
        $model_registry['data_object_db'] = 'molajo';
        $model_registry['data_object_db_host'] = 'localhost';
        $model_registry['data_object_db_password'] = '';
        $model_registry['data_object_db_prefix'] = 'molajo_';
        $model_registry['data_object_db_type'] = 'mysqli';
        $model_registry['data_object_db_user'] = '';
        $model_registry['data_object_model_name'] = 'Database';
        $model_registry['data_object_model_type'] = 'Dataobject';
        $model_registry['data_object_name'] = 'Database';
        $model_registry['data_object_process_events'] = 1;
        $model_registry['fields'] = array();
        $model_registry['joins'] = array();
        $model_registry['joinfields'] = array();
        $model_registry['foreignkeys'] = array();
        $model_registry['criteria'] = array();
        $model_registry['children'] = array();
        $model_registry['customfieldgroups'] = array();
        $model_registry['model_offset'] = 0;
        $model_registry['model_count'] = 20;
        $model_registry['fields']['0'] = array();
        $model_registry['fields']['1'] = array();
        $model_registry['fields']['2'] = array();
        $model_registry['fields']['3'] = array();
        $model_registry['fields']['4'] = array();
        $model_registry['fields']['5'] = array();
        $model_registry['fields']['6'] = array();
        $model_registry['fields']['7'] = array();
        $model_registry['fields']['8'] = array();
        $model_registry['fields']['9'] = array();
        $model_registry['fields']['10'] = array();
        $model_registry['fields']['0']['name'] = 'id';
        $model_registry['fields']['0']['type'] = 'integer';
        $model_registry['fields']['0']['null'] = 1;
        $model_registry['fields']['0']['default'] = 0;
        $model_registry['fields']['0']['identity'] = 1;
        $model_registry['fields']['1']['name'] = 'application_id';
        $model_registry['fields']['1']['type'] = 'integer';
        $model_registry['fields']['1']['null'] = 0;
        $model_registry['fields']['1']['default'] = '';
        $model_registry['fields']['2']['name'] = 'catalog_type_id';
        $model_registry['fields']['2']['type'] = 'integer';
        $model_registry['fields']['2']['null'] = 0;
        $model_registry['fields']['2']['default'] = 'model';
        $model_registry['fields']['2']['datalist'] = 'CatalogType';
        $model_registry['fields']['2']['locked'] = 0;
        $model_registry['fields']['2']['display'] = 'catalog_types_title';
        $model_registry['fields']['3']['name'] = 'source_id';
        $model_registry['fields']['3']['type'] = 'integer';
        $model_registry['fields']['3']['null'] = 0;
        $model_registry['fields']['3']['default'] = ' ';
        $model_registry['fields']['3']['hidden'] = 1;
        $model_registry['fields']['4']['name'] = 'enabled';
        $model_registry['fields']['4']['type'] = 'boolean';
        $model_registry['fields']['4']['null'] = 0;
        $model_registry['fields']['4']['default'] = 0;
        $model_registry['fields']['5']['name'] = 'redirect_to_id';
        $model_registry['fields']['5']['type'] = 'integer';
        $model_registry['fields']['5']['null'] = 0;
        $model_registry['fields']['5']['default'] = 0;
        $model_registry['fields']['6']['name'] = 'sef_request';
        $model_registry['fields']['6']['type'] = 'string';
        $model_registry['fields']['6']['null'] = 0;
        $model_registry['fields']['6']['default'] = ' ';
        $model_registry['fields']['7']['name'] = 'page_type';
        $model_registry['fields']['7']['type'] = 'string';
        $model_registry['fields']['7']['null'] = 0;
        $model_registry['fields']['7']['default'] = ' ';
        $model_registry['fields']['7']['datalist'] = 'Pagetypes';
        $model_registry['fields']['8']['name'] = 'extension_instance_id';
        $model_registry['fields']['8']['type'] = 'integer';
        $model_registry['fields']['8']['null'] = 0;
        $model_registry['fields']['8']['default'] = 0;
        $model_registry['fields']['8']['datalist'] = 'ExtensionInstances';
        $model_registry['fields']['8']['locked'] = 1;
        $model_registry['fields']['8']['display'] = 'name';
        $model_registry['fields']['9']['name'] = 'view_group_id';
        $model_registry['fields']['9']['type'] = 'integer';
        $model_registry['fields']['9']['null'] = 0;
        $model_registry['fields']['9']['default'] = 0;
        $model_registry['fields']['10']['name'] = 'primary_category_id';
        $model_registry['fields']['10']['type'] = 'integer';
        $model_registry['fields']['10']['null'] = 0;
        $model_registry['fields']['10']['default'] = 0;
        $model_registry['joins']['0'] = array();
        $model_registry['joins']['1'] = array();
        $model_registry['joins']['2'] = array();
        $model_registry['joins']['0']['table_name'] = '#__catalog_types';
        $model_registry['joins']['0']['alias'] = 'b';
        $model_registry['joins']['0']['select'] = 'title,model_type,model_name,primary_category_id,alias';
        $model_registry['joins']['0']['jointo'] = 'id';
        $model_registry['joins']['0']['joinwith'] = 'catalog_type_id';
        $model_registry['joins']['1']['table_name'] = '#__application_extension_instances';
        $model_registry['joins']['1']['alias'] = 'application_extension_instances';
        $model_registry['joins']['1']['select'] = '';
        $model_registry['joins']['1']['jointo'] = 'application_id,extension_instance_id';
        $model_registry['joins']['1']['joinwith'] = 'APPLICATION_ID,extension_instance_id';
        $model_registry['joins']['2']['table_name'] = '#__site_extension_instances';
        $model_registry['joins']['2']['alias'] = 'site_extension_instances';
        $model_registry['joins']['2']['select'] = '';
        $model_registry['joins']['2']['jointo'] = 'site_id,extension_instance_id';
        $model_registry['joins']['2']['joinwith'] = 'SITE_ID,extension_instance_id';
        $model_registry['joinfields']['0'] = array();
        $model_registry['joinfields']['1'] = array();
        $model_registry['joinfields']['2'] = array();
        $model_registry['joinfields']['0']['name'] = 'protected';
        $model_registry['joinfields']['0']['type'] = 'boolean';
        $model_registry['joinfields']['0']['null'] = 0;
        $model_registry['joinfields']['0']['default'] = 0;
        $model_registry['joinfields']['0']['locked'] = 1;
        $model_registry['joinfields']['0']['hidden'] = 1;
        $model_registry['joinfields']['1']['name'] = 'extension_instance_id';
        $model_registry['joinfields']['1']['type'] = 'integer';
        $model_registry['joinfields']['1']['null'] = 0;
        $model_registry['joinfields']['1']['default'] = 0;
        $model_registry['joinfields']['1']['datalist'] = 'ExtensionInstances';
        $model_registry['joinfields']['1']['locked'] = 1;
        $model_registry['joinfields']['1']['display'] = 'name';
        $model_registry['joinfields']['2']['name'] = 'extension_instance_id';
        $model_registry['joinfields']['2']['type'] = 'integer';
        $model_registry['joinfields']['2']['null'] = 0;
        $model_registry['joinfields']['2']['default'] = 0;
        $model_registry['joinfields']['2']['datalist'] = 'ExtensionInstances';
        $model_registry['joinfields']['2']['locked'] = 1;
        $model_registry['joinfields']['2']['display'] = 'name';
        $model_registry['foreignkeys']['0'] = array();
        $model_registry['foreignkeys']['0']['name'] = 'catalog_type_id';
        $model_registry['foreignkeys']['0']['source_id'] = 'id';
        $model_registry['foreignkeys']['0']['source_model'] = 'CatalogTypes';
        $model_registry['foreignkeys']['0']['required'] = 1;
        $model_registry['criteria']['0'] = array();
        $model_registry['criteria']['1'] = array();
        $model_registry['criteria']['0']['name'] = 'a.enabled';
        $model_registry['criteria']['0']['connector'] = '=';
        $model_registry['criteria']['0']['value'] = 1;
        $model_registry['criteria']['1']['name'] = 'a.redirect_to_id';
        $model_registry['criteria']['1']['connector'] = '=';
        $model_registry['criteria']['1']['value'] = 0;
        $model_registry['children']['0'] = array();
        $model_registry['children']['1'] = array();
        $model_registry['children']['0']['name'] = 'Catalogactivity';
        $model_registry['children']['0']['type'] = 'Datasource';
        $model_registry['children']['0']['join'] = 'catalog_id';
        $model_registry['children']['1']['name'] = 'Catalogcategories';
        $model_registry['children']['1']['type'] = 'Datasource';
        $model_registry['children']['1']['join'] = 'catalog_id';

        return $model_registry;
    }
}

/**
 * Class MockModelRegistryTests
 *
 * Generates the assert statements for testing a registry
 *
 * @package Molajo\Controller
 */
class MockModelRegistryTests
{
    protected $model_registry;


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
        //  $this->generate();

        return $this->model_registry;
    }
    public function generate()
    {
        $field1_arrays = $this->echoModelRegistryEntries($this->model_registry);
        foreach ($field1_arrays as $field1_key) {
            $field2_arrays = $this->echoModelRegistryEntries($this->model_registry[$field1_key], $field1_key);
            foreach ($field2_arrays as $field2_key) {
                $this->echoModelRegistryEntries(
                    $this->model_registry[$field1_key][$field2_key],
                    $field1_key,
                    $field2_key
                );
            }
        }
    }

    public function echoModelRegistryEntries($model_registry, $field1 = null, $field2 = null)
    {
        $more_arrays = array();

        foreach ($model_registry as $key => $value) {

            if (is_numeric($value)) {

            } elseif (is_array($value)) {
                $value = "array()";
                $more_arrays[] = $key;

            } else {
                $value = "'" . $value . "'";
            }

            $use_field = '';

            if ($field1 === null) {
            } else {
                $use_field .= "['" . $field1 . "']";
            }

            if ($field2 === null) {
            } else {
                $use_field .= "['" . $field2 . "']";
            }

            $use_field .= "['" . $key . "']";

            if ($value === "array()") {
                echo '$this->assertTrue(is_array($model_registry' . $use_field . '));' . '<br>';
            } else {
                echo '$this->assertEquals(' . $value . ', $model_registry' . $use_field . ');' . '<br>';
            }

        }

        return $more_arrays;
    }
}
