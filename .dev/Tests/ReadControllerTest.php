<?php
/**
 * Controller Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use ReflectionClass;
use Molajo\Fieldhandler\MockRequest as Fieldhandler;
use Molajo\Database\MockDatabase as Database;
use Molajo\Query\Adapter\Mysql as QueryClass;
use Molajo\Query\QueryProxy;
use Molajo\Model\ReadModel;
use Molajo\Query\Model\Registry;
use Molajo\Query\QueryBuilder;

use PHPUnit_Framework_TestCase;

/**
 * Controller
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ReadControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;

    /**
     * Test Get Method
     *
     * @covers  Molajo\Controller\ReadController::getData
     * @covers  Molajo\Controller\ReadController::runQuery
     * @covers  Molajo\Controller\ReadController::executeQuery
     * @covers  Molajo\Controller\ReadController::processPagination
     * @covers  Molajo\Controller\ReadController::processPaginationItem
     * @covers  Molajo\Controller\ReadController::returnQueryResults
     * @covers  Molajo\Controller\ReadController::triggerOnBeforeReadEvent
     * @covers  Molajo\Controller\ReadController::triggerOnAfterReadEvent
     * @covers  Molajo\Controller\ReadController::triggerOnAfterReadallEvent
     * @covers  Molajo\Controller\ReadController::triggerEvent
     * @covers  Molajo\Controller\ReadController::prepareEventInput
     * @covers  Molajo\Controller\ReadController::processEventResults
     *
     * @covers  Molajo\Controller\QueryController::__construct
     * @covers  Molajo\Controller\QueryController::getModelRegistry
     * @covers  Molajo\Controller\QueryController::setModelRegistry
     * @covers  Molajo\Controller\QueryController::getSql
     * @covers  Molajo\Controller\QueryController::clearQuery
     * @covers  Molajo\Controller\QueryController::setType
     * @covers  Molajo\Controller\QueryController::getDateFormat
     * @covers  Molajo\Controller\QueryController::getDate
     * @covers  Molajo\Controller\QueryController::getNullDate
     * @covers  Molajo\Controller\QueryController::setDistinct
     * @covers  Molajo\Controller\QueryController::select
     * @covers  Molajo\Controller\QueryController::from
     * @covers  Molajo\Controller\QueryController::whereGroup
     * @covers  Molajo\Controller\QueryController::where
     * @covers  Molajo\Controller\QueryController::groupBy
     * @covers  Molajo\Controller\QueryController::havingGroup
     * @covers  Molajo\Controller\QueryController::having
     * @covers  Molajo\Controller\QueryController::orderBy
     * @covers  Molajo\Controller\QueryController::setOffsetAndLimit
     * @covers  Molajo\Controller\QueryController::get
     *
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @return void
     * @since   1.0
     */
    public function setup()
    {
        /** Fieldhandler */
        $fieldhandler = new Fieldhandler();

        /** Database */
        $database_prefix = 'molajo_';
        $database        = new Database();

        /** Query Proxy */
        $query_class     = new QueryClass($fieldhandler, 'molajo_', $database);
        $query_proxy     = new QueryProxy($query_class);

        /** Model Registry */
        $model_registry = array();
        $registry       = new Registry($query_proxy, $model_registry);

        /** Query Builder */
        $query_builder  = new QueryBuilder($registry);

        /** Model */
        $model = new ReadModel($database);

        /** Read Controller */
        $this->controller = new ReadController(
            $model,
            $runtime_data = array(),
            $plugin_data = array(),
            $schedule_event = 'strtolower',
            $query_builder
        );
    }

    /**
     * Test Constructor
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @return void
     * @since   1.0
     */
    public function testConstruct()
    {
        //      $this->assertEquals(2, $this->controller->getValue('site_id'));

        return;
    }
}
