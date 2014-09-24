<?php
/**
 * Controller Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use Molajo\Database\MockDatabase;
use Molajo\Model\ReadModel;
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
     * @covers  Molajo\Controller\QueryController::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
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
     * @covers  Molajo\Model\ReadModel::getData
     * @covers  Molajo\Model\Model::__construct
     * @covers  Molajo\Model\Model::get
     * @covers  Molajo\Model\Model::set
     *
     * @return void
     * @since   1.0
     */
    public function setup()
    {
        /**
        $class   = new \ReflectionClass('Molajo\Query\QueryBuilder');
        $methods = $class->getMethods();
        foreach ($methods as $method) {
            echo '     * @covers  ' . $method->class . '::' . $method->name . PHP_EOL;
        }
        die;
        */

        $database       = new MockDatabase();
        $model          = new ReadModel($database);
        $runtime_data   = array();
        $schedule_event = 'strtolower';

        $this->controller = new MockController(
            $model,
            $runtime_data,
            $schedule_event
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
        $this->assertEquals(2, $this->controller->getValue('site_id'));
        $this->assertEquals(2, $this->controller->getValue('application_id'));

        return;
    }

    /**
     * Test Set Method
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @return void
     * @since   1.0
     */
    public function testSet()
    {
        $this->controller->setValue('site_id', 100);
        $this->assertEquals(100, $this->controller->getValue('site_id'));

        return;
    }
}
