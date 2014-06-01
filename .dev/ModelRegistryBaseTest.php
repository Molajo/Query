<?php
/**
 * Query Model Registry Base Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Model;

use PHPUnit_Framework_TestCase;

/**
 * Query Model Registry Base Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ModelRegistryBaseTest extends PHPUnit_Framework_TestCase
{
    protected $model_registry;

    /**
     * Test Get Method
     *
     * @covers  Molajo\Query\Model::__construct
     * @covers  Molajo\Query\Model::setDateProperties
     *
     * @return void
     * @since   1.0
     */
    public function setup()
    {
        $this->model_registry = $this->getMockForAbstractClass('Molajo\Query\Model\Query');
    }

    /**
     * Test setDateProperties
     *
     * @covers  Molajo\Query\Model::__construct
     * @covers  Molajo\Query\Model::setDateProperties
     *
     * @return void
     * @since   1.0
     */
    public function testConstruct()
    {
        $this->model_registry->expects($this->any())
            ->method('setDateProperties')
            ->will($this->returnValue('Molajo\Query\Model\Query'));

        $this->assertTrue($this->model_registry->concreteMethod());

        return;
    }
}
