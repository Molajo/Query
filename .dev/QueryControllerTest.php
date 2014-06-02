<?php
/**
 * Query Controller Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Controller\ControllerInterface;
use CommonApi\Database\DatabaseInterface;
use CommonApi\Model\ModelInterface;
use CommonApi\Query\QueryInterface2;
use Molajo\Database\MockDatabase;
use Molajo\Model\ReadModel;
use Molajo\Query\MockQuery;
use PHPUnit_Framework_TestCase;

/**
 * Query Controller Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class QueryControllerTest extends PHPUnit_Framework_TestCase
{
    protected $controller;

    /**
     * Test Get Method
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @return void
     * @since   1.0
     */
    public function setup()
    {
        $database       = new MockDatabase();
        $model          = new ReadModel($database);
        $runtime_data   = array();
        $plugin_data    = array();
        $schedule_event = 'strtolower';

        $this->controller = new MockController(
            $model,
            $runtime_data,
            $plugin_data,
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
