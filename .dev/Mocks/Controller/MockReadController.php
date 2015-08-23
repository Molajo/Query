<?php
/**
 * Mock Read Controller
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Query\ControllerInterface;
use CommonApi\Query\ModelInterface;
use CommonApi\Query\QueryBuilderInterface;

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
        callable $schedule_event = null,
        QueryBuilderInterface $query = null
    ) {
        parent::__construct(
            $model,
            $runtime_data,
            $schedule_event,
            $query
        );
    }
}
