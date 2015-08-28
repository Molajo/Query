<?php
/**
 * Configuration Factory
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Adapter;

use CommonApi\Exception\RuntimeException;
use Exception;

/**
 * XML Handler
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
abstract class ConfigurationFactory extends NamespaceHandler
{
    /**
     * Data Object
     *
     * @var    object
     * @since  1.0.0
     */
    protected $data;

    /**
     * Resource
     *
     * @var    object  CommonApi\Resource\ResourceInterface
     * @since  1.0.0
     */
    protected $resource;

    /**
     * Registry
     *
     * @var    object  Molajo\Resource\Api\RegistryInterface
     * @since  1.0.0
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param  string $base_path
     * @param  array  $resource_map
     * @param  array  $namespace_prefixes
     * @param  array  $valid_file_extensions
     * @param  array  $cache_callbacks
     * @param  array  $handler_options
     *
     * @since  1.0.0
     */
    public function __construct(
        $base_path,
        array $resource_map = array(),
        array $namespace_prefixes = array(),
        array $valid_file_extensions = array(),
        array $cache_callbacks = array(),
        array $handler_options = array()
    ) {
        parent::__construct(
            $base_path,
            $resource_map,
            $namespace_prefixes,
            $valid_file_extensions,
            $cache_callbacks
        );

        $this->saveConfigurationDependencies($handler_options);
    }

    /**
     * Save Configuration Dependencies
     *
     * @param   array $handler_options
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function saveConfigurationDependencies(array $handler_options = array())
    {
        if (count($handler_options) === 0) {
            return $this;
        }

        $this->data     = $handler_options['data'];
        $this->resource = $handler_options['resource'];
        $this->registry = $handler_options['registry'];

        return $this;
    }

    /**
     * Create Model Configuration Class
     *
     * @return  object
     * @since   1.0.0
     */
    public function instantiateModelConfiguration()
    {
        $class = 'Molajo\\Resource\\Configuration\\Model';

        $data     = $this->data;
        $registry = $this->registry;
        $resource = $this->resource;

        try {
            return new $class ($data, $registry, $resource);

        } catch (Exception $e) {
            throw new RuntimeException(
                'Resource ConfigurationFactory instantiateModelConfiguration failed: '
                . $e->getMessage()
            );
        }
    }

    /**
     * Create data_object Configuration Class
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function instantiateDataObjectConfiguration()
    {
        $class = 'Molajo\\Resource\\Configuration\\Dataobject';

        $data     = $this->data;
        $registry = $this->registry;
        $resource = $this->resource;

        try {
            return new $class ($data, $registry, $resource);

        } catch (Exception $e) {
            throw new RuntimeException(
                'Resource ConfigurationFactory instantiateDataObjectConfiguration failed: '
                . $e->getMessage()
            );
        }
    }
}
