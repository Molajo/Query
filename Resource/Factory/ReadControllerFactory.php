<?php
/**
 * Read Controller Factory
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Factory;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Controller\ReadControllerInterface;
use Molajo\Resource\Api\FactoryInterface;

/**
 * Read Controller Factory
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0
 */
class ReadControllerFactory implements FactoryInterface
{
    /**
     * Model Instance  CommonApi\Controller\ReadControllerInterface
     *
     * @var    object
     * @since  1.0
     */
    protected $model;

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data = null;

    /**
     * Plugin Data
     *
     * @var    object
     * @since  1.0
     */
    protected $plugin_data = null;

    /**
     * Schedule Event - anonymous function to FrontController schedule_event method
     *
     * @var    callable
     * @since  1.0
     */
    protected $schedule_event;

    /**
     * SQL
     *
     * @var    string
     * @since  1.0
     */
    protected $sql;

    /**
     * Constructor
     *
     * @param  ReadControllerInterface $model
     * @param  object                  $runtime_data
     * @param  object                  $plugin_data
     * @param  callback                $schedule_event
     * @param  string                  $sql
     *
     * @since  1.0
     */
    public function __construct(
        ReadControllerInterface $model,
        $runtime_data,
        $plugin_data,
        callable $schedule_event,
        $sql = ''
    ) {
        $this->model          = $model;
        $this->runtime_data   = $runtime_data;
        $this->plugin_data    = $plugin_data;
        $this->schedule_event = $schedule_event;
        $this->sql            = $sql;
    }

    /**
     * Instantiate Class
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $class = 'Molajo\\Controller\\ReadController';

        try {
            return new $class (
                $this->model,
                $this->runtime_data,
                $this->plugin_data,
                $this->schedule_event,
                $this->sql
            );
        } catch (Exception $e) {
            throw new RuntimeException ('Resource Factory ReadControllerFactory failed in class instantiation. '
            . $e->getMessage());
        }
    }
}
