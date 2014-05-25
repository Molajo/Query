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
use Molajo\Query\Adapter\MySql as MySQLQueryAdapter;
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
     * @covers  Molajo\Controller\ModelRegistry::__construct
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaults
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistry::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistry::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistry::setProperty
     *
     * @return void
     * @since   1.0
     */
    public function testConstruct()
    {

        $this->assertEquals('#__content', $this->controller->getModelRegistry('table_name'));
/**

        $dispatcher->registerForEvent('onBeforeRead', $x, 2);
        $dispatcher->registerForEvent('onBeforeRead', $y, 1);
        $dispatcher->registerForEvent('onBeforeUpdate', $y, 1);
        $dispatcher->registerForEvent('onAfterDelete', $x, 3);
        $dispatcher->registerForEvent('onAfterDelete', $y, 2);
        $dispatcher->registerForEvent('onAfterDelete', $z, 1);

        $return = array('a', 'e');
        $data = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);
        $event_instance = new Scheduled('onBeforeRead', $return, $data);

        $results = $dispatcher->scheduleEvent('onBeforeRead', $event_instance);

        $this->assertEquals(100, $results['a']);
        $this->assertEquals(5, $results['e']);
        $this->assertEquals(2, count($results));
*/
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
