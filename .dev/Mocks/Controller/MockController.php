<?php
/**
 * Mock Controller
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Controller\ControllerInterface;
use CommonApi\Model\ModelInterface;
use CommonApi\Query\QueryInterface;

/**
 * Class MockModelRegistry
 *
 * Generates a registry for testing when used with the web and ModelRegistryQuery
 *
 * @package Molajo\Controller
 */

class MockController extends Controller implements ControllerInterface
{
    public function __construct(
        QueryInterface $query,
        ModelInterface $model = null,
        $runtime_data = array(),
        $plugin_data = array(),
        callable $schedule_event = null,
        array $model_registry = array()
    ) {
        parent::__construct(
            $query,
            $model,
            $runtime_data,
            $plugin_data,
            $schedule_event
        );
    }
}
