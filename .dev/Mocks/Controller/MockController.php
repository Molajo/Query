<?php
/**
 * Mock Controller
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Query\ControllerInterface;
use CommonApi\Query\ModelInterface;

/**
 * Class MockModelRegistry
 *
 * Generates a registry for testing when used with the web and ModelRegistryQuery
 *
 * @package Molajo\Controller
 */
class MockController extends Base implements ControllerInterface
{
    public function __construct(
        ModelInterface $model = null,
        $runtime_data = array(),
        callable $schedule_event = null
    ) {
        parent::__construct(
            $model,
            $runtime_data,
            $schedule_event
        );
    }
}
