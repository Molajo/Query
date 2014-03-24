<?php
/**
 * Read Model Factory
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Factory;

use Exception;
use CommonApi\Authorisation\AuthorisationInterface;
use CommonApi\Cache\CacheInterface;
use CommonApi\Database\DatabaseInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Model\FieldhandlerInterface;
use CommonApi\Controller\ReadControllerInterface;

/**
 * Read Model Factory
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0
 */
class ReadModelFactory
{
    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    public $model_registry = null;

    /**
     * Database Instance
     *
     * @var    object   CommonApi\Database\DatabaseInterface
     * @since  1.0
     */
    public $database = null;

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
     * Filter Fields Instance
     *
     * @var    object  CommonApi\Model\FieldhandlerInterface
     * @since  1.0
     */
    protected $fieldhandler;

    /**
     * Authorisation Instance
     *
     * @var    object  CommonApi\Authorisation\AuthorisationInterface
     * @since  1.0
     */
    protected $authorisation = null;

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
     * Constructor
     *
     * @param  object                 $model_registry
     * @param  DatabaseInterface      $database
     * @param  string                 $null_date
     * @param  string                 $current_date
     * @param  FieldhandlerInterface  $fieldhandler
     * @param  AuthorisationInterface $authorisation
     * @param  CacheInterface         $cache
     * @param  int                    $site_id
     * @param  int                    $application_id
     *
     * @since  1.0
     */
    public function __construct(
        $model_registry = null,
        DatabaseInterface $database,
        $null_date,
        $current_date,
        FieldhandlerInterface $fieldhandler,
        AuthorisationInterface $authorisation,
        CacheInterface $cache = null,
        $site_id,
        $application_id
    ) {
        $this->model_registry = $model_registry;
        $this->database       = $database;
        $this->null_date      = $null_date;
        $this->current_date   = $current_date;
        $this->fieldhandler   = $fieldhandler;
        $this->authorisation  = $authorisation;
        $this->cache          = $cache;
        $this->site_id        = $site_id;
        $this->application_id = $application_id;
    }

    /**
     * Create Model Instance
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Controller\ReadControllerInterface
     */
    public function instantiateClass()
    {
        $class = 'Molajo\\Model\\ReadModel';

        $query = $this->database->getQueryObject();

        try {
            return new $class (
                $this->model_registry,
                $this->database,
                $query,
                $this->null_date,
                $this->current_date,
                $this->fieldhandler,
                $this->authorisation,
                $this->cache,
                $this->site_id,
                $this->application_id
            );
        } catch (Exception $e) {
            throw new RuntimeException ('Resource Query Handler Failed Instantiating Model: '
            . $e->getMessage());
        }
    }
}
