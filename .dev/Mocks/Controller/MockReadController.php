<?php
/**
 * Mock Read Controller
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Controller\ControllerInterface;
use CommonApi\Model\ModelInterface;
use CommonApi\Query\QueryBuilderInterface2;

/**
 * Class MockReadController
 *
 * @package Molajo\Controller
 */
class MockReadController extends QueryController implements ControllerInterface
{
    public function __construct(
        ModelInterface $model = null,
        $runtime_data = array(),
        $plugin_data = array(),
        callable $schedule_event = null,
        QueryBuilderInterface2 $query = null
    ) {
        parent::__construct(
            $model,
            $runtime_data,
            $plugin_data,
            $schedule_event,
            $query
        );
    }
}
