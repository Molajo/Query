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
use CommonApi\Cache\CacheInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Model\ModelInterface;
use CommonApi\Query\QueryInterface;
use Molajo\Resource\Api\FactoryInterface;

/**
 * Read Controller Factory
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ReadControllerFactory implements FactoryInterface
{
    /**
     * Query Instance
     *
     * @var    object  CommonApi\Query\QueryInterface
     * @since  1.0
     */
    protected $query = null;

    /**
     * Model Instance
     *
     * @var    object  CommonApi\Controller\ReadControllerInterface
     * @since  1.0
     */
    protected $model;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $model_registry = null;

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
     * Cache
     *
     * @var    object  CommonApi\Cache\CacheInterface
     * @since  1.0
     */
    protected $cache;

    /**
     * Site ID
     *
     * @var    int
     * @since  1.0
     */
    protected $site_id = null;

    /**
     * Application ID
     *
     * @var    int
     * @since  1.0
     */
    protected $application_id = null;

    /**
     * Used in queries to determine date validity
     *
     * @var    string
     * @since  1.0
     */
    protected $null_date;

    /**
     * Today's CCYY-MM-DD 00:00:00 formatted for query
     *
     * @var    string
     * @since  1.0
     */
    protected $current_date;

    /**
     * Constructor
     *
     * @param  QueryInterface $query
     * @param  ModelInterface $model
     * @param  array          $model_registry
     * @param  object         $runtime_data
     * @param  object         $plugin_data
     * @param  callback       $schedule_event
     * @param  string         $sql
     * @param  CacheInterface         $cache
     * @param  int            $site_id
     * @param  int            $application_id
     * @param  string         $null_date
     * @param  string         $current_date
     *
     * @since  1.0
     */
    public function __construct(
        QueryInterface $query,
        ModelInterface $model,
        $model_registry,
        $runtime_data,
        $plugin_data,
        callable $schedule_event,
        $sql = '',
        CacheInterface $cache,
        $site_id = 0,
        $application_id = 0,
        $null_date = '',
        $current_date = ''
    ) {
        $this->query          = $query;
        $this->model          = $model;
        $this->model_registry = $model_registry;
        $this->runtime_data   = $runtime_data;
        $this->plugin_data    = $plugin_data;
        $this->schedule_event = $schedule_event;
        $this->sql            = $sql;
        $this->cache          = $cache;
        $this->site_id        = $site_id;
        $this->application_id = $application_id;
        $this->null_date      = $null_date;
        $this->current_date   = $current_date;

        $this->query->clearQuery();
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
                $this->query,
                $this->model,
                $this->model_registry,
                $this->runtime_data,
                $this->plugin_data,
                $this->schedule_event,
                $this->sql,
                $this->cache,
                $this->site_id,
                $this->application_id,
                $this->null_date,
                $this->current_date
            );
        } catch (Exception $e) {
            throw new RuntimeException
            ('Resource Factory ReadControllerFactory failed in instantiateClass Method.' . $e->getMessage());
        }
    }
}
