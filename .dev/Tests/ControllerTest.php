<?php
/**
 * Controller Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Cache\CacheInterface;
use CommonApi\Controller\ControllerInterface;
use CommonApi\Database\DatabaseInterface;
use CommonApi\Model\ModelInterface;
use CommonApi\Query\QueryInterface;

use Molajo\Model\ReadModel;
use Molajo\Query\Driver as QueryDriver;
use Molajo\Query\Adapter\Mysql as MySQLQueryAdapter;
use Molajo\Fieldhandler\Request as FieldhandlerRequest;

use PHPUnit_Framework_TestCase;

/**
 * Controller
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;

    public function setup()
    {
        $field_handler = new FieldhandlerRequest();
        $database = new MockDatabaseforController();

        $query_adapter = new MockQueryAdapterforController($field_handler, 'molajo_', $database);
        $query = new MockQueryforController($query_adapter);

        $model = new ReadModel($database);
        $cache = null;

        $model_registry = array();
        $runtime_data = array();
        $plugin_data = array();
        $schedule_event = 'strtolower';
        $sql = '';
        $null_date = '0000/00/00 00:00:00';
        $current_date = '2014/06/01 12:30:00';
        $cache = null;
        $site_id = 0;
        $application_id = 0;

        $this->controller = new MockController(
            $query,
            $model,
            $model_registry,
            $runtime_data,
            $plugin_data,
            $schedule_event,
            $sql,
            $null_date,
            $current_date,
            $cache,
            $site_id,
            $application_id
        );
    }

    /**
     * Test Get Method
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::getModelRegistry
     * @covers  Molajo\Controller\Controller::getModelRegistryAll
     * @covers  Molajo\Controller\Controller::getModelRegistryByKey
     * @covers  Molajo\Controller\Controller::setModelRegistry
     * @covers  Molajo\Controller\Controller::setModelProperties
     * @covers  Molajo\Controller\Controller::setModelRegistryDefaults
     * @covers  Molajo\Controller\Controller::setDateProperties
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @covers  Molajo\Controller\ModelRegistryDefaults::__construct
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaults
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setProperty
     *
     * @return void
     * @since   1.0
     */
    public function testConstruct()
    {
        $this->assertEquals('#__content', $this->controller->getModelRegistry('table_name'));

        return;
    }

    /**
     * Test Get and Set Methods
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::getModelRegistry
     * @covers  Molajo\Controller\Controller::getModelRegistryAll
     * @covers  Molajo\Controller\Controller::getModelRegistryByKey
     * @covers  Molajo\Controller\Controller::setModelRegistry
     * @covers  Molajo\Controller\Controller::setModelProperties
     * @covers  Molajo\Controller\Controller::setModelRegistryDefaults
     * @covers  Molajo\Controller\Controller::setDateProperties
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @covers  Molajo\Controller\ModelRegistryDefaults::__construct
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaults
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setProperty
     *
     * @return void
     * @since   1.0
     */
    public function testGetSet()
    {
        $this->assertEquals(2, $this->controller->getValue('site_id'));
        $this->controller->setValue('site_id', 1);
        $this->assertEquals(1, $this->controller->getValue('site_id'));

        return;
    }

    /**
     * Test Get and Set Model Registry
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::getModelRegistry
     * @covers  Molajo\Controller\Controller::getModelRegistryAll
     * @covers  Molajo\Controller\Controller::getModelRegistryByKey
     * @covers  Molajo\Controller\Controller::setModelRegistry
     * @covers  Molajo\Controller\Controller::setModelProperties
     * @covers  Molajo\Controller\Controller::setModelRegistryDefaults
     * @covers  Molajo\Controller\Controller::setDateProperties
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @covers  Molajo\Controller\ModelRegistryDefaults::__construct
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaults
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setProperty
     *
     * @return void
     * @since   1.0
     */
    public function testGetSetModelRegistry()
    {
        $this->controller->setModelRegistry('table_name', 'dog');
        $this->assertEquals('dog', $this->controller->getModelRegistry('table_name'));

        return;
    }

    /**
     * Test Get and Set Model Registry
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::getModelRegistry
     * @covers  Molajo\Controller\Controller::getModelRegistryAll
     * @covers  Molajo\Controller\Controller::getModelRegistryByKey
     * @covers  Molajo\Controller\Controller::setModelRegistry
     * @covers  Molajo\Controller\Controller::setModelProperties
     * @covers  Molajo\Controller\Controller::setModelRegistryDefaults
     * @covers  Molajo\Controller\Controller::setDateProperties
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @covers  Molajo\Controller\ModelRegistryDefaults::__construct
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaults
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setProperty
     *
     * @return void
     * @since   1.0
     */
    public function testGetModelRegistry()
    {
        $model_registry = $this->controller->getModelRegistry();

        $this->assertEquals(1, $model_registry['process_events']);
        $this->assertEquals('', $model_registry['criteria_status']);
        $this->assertEquals(0, $model_registry['criteria_source_id']);
        $this->assertEquals(0, $model_registry['criteria_catalog_type_id']);
        $this->assertEquals(0, $model_registry['catalog_type_id']);
        $this->assertEquals(0, $model_registry['menu_id']);
        $this->assertEquals(0, $model_registry['criteria_extension_instance_id']);
        $this->assertEquals('id', $model_registry['primary_key']);
        $this->assertEquals(null, $model_registry['name_key_value']);
        $this->assertEquals(array(), $model_registry['fields']);
        $this->assertEquals('a', $model_registry['primary_prefix']);
        $this->assertEquals('list', $model_registry['query_object']);
        $this->assertEquals(array(), $model_registry['criteria']);
        $this->assertEquals(0, $model_registry['use_special_joins']);
        $this->assertEquals(array(), $model_registry['joins']);
        $this->assertEquals(15, $model_registry['model_count']);
        $this->assertEquals(1, $model_registry['use_pagination']);

        return;
    }

    /**
     * Tear down
     *
     * @return void
     * @since   1.0
     */
    protected function tearDown()
    {
        parent::tearDown();
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
class MockController extends Controller implements ControllerInterface
{
}

class MockQueryforController extends QueryDriver implements QueryInterface
{
}

class MockQueryAdapterforController extends MySQLQueryAdapter implements QueryInterface
{
}

class MockDatabaseforController implements DatabaseInterface
{
    public function escape($value) {

    }

    public function loadResult($sql) {

    }

    public function loadObjectList($sql) {

    }

    public function execute($sql) {

    }

    public function getInsertId() {

    }
}
class MockReadModel extends ReadModel implements ModelInterface
{

}
