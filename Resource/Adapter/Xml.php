<?php
/**
 * XML Handler
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Adapter;

use Exception;
use CommonApi\Resource\AdapterInterface;
use CommonApi\Exception\RuntimeException;
use Molajo\Resource\Api\ConfigurationInterface;

/**
 * XML Handler
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Xml extends AbstractAdapter implements AdapterInterface
{
    /**
     * Model Handler
     *
     * @var    object Molajo\Resource\Configuration\ModelHandler
     * @since  1.0
     */
    protected $model_handler;

    /**
     * Data Object Handler
     *
     * @var    object Molajo\Resource\Configuration\DataobjectHandler
     * @since  1.0
     */
    protected $dataobject_handler;

    /**
     * Constructor
     *
     * @param  string                 $base_path
     * @param  array                  $resource_map
     * @param  array                  $namespace_prefixes
     * @param  array                  $valid_file_extensions
     * @param  ConfigurationInterface $model_handler
     * @param  ConfigurationInterface $dataobject_handler
     *
     * @since  1.0
     */
    public function __construct(
        $base_path,
        array $resource_map = array(),
        array $namespace_prefixes = array(),
        array $valid_file_extensions = array(),
        ConfigurationInterface $model_handler = null,
        ConfigurationInterface $dataobject_handler = null
    ) {
        parent::__construct(
            $base_path,
            $resource_map,
            $namespace_prefixes,
            $valid_file_extensions
        );

        $this->model_handler      = $model_handler;
        $this->dataobject_handler = $dataobject_handler;
    }

    /**
     * Set a namespace prefix by mapping to the filesystem path
     *
     * @param   string  $namespace_prefix
     * @param   string  $namespace_base_directory
     * @param   boolean $prepend
     *
     * @return  $this
     * @since   1.0
     */
    public function setNamespace($namespace_prefix, $namespace_base_directory, $prepend = false)
    {
        return parent::setNamespace($namespace_prefix, $namespace_base_directory, $prepend);
    }

    /**
     * Locates folder/file associated with Namespace for Resource
     *
     * @param   string $resource_namespace
     * @param   bool   $multiple
     *
     * @return  array|mixed|string|void
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function get($resource_namespace, $multiple = false)
    {
        return parent::get($resource_namespace);
    }

    /**
     * Xml file is located, read, loaded using Simplexml into a string and then sent back
     *  or processed by the Configuration Dataobject or Model utility
     *
     * @param   string $scheme
     * @param   string $located_path
     * @param   array  $options
     *
     * @return  void|mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function handlePath($scheme, $located_path, array $options = array())
    {
        if (isset($options['namespace'])) {
        } else {
            throw new RuntimeException
            ('Resource XmlHandler handlePath options array must have namespace entry.');
        }

        $segments = explode('//', $options['namespace']);

        if (count($segments) > 2) {
        } else {
            echo '<pre>';
            var_dump($segments);
            throw new RuntimeException
            ('Resource XmlHandler Failure namespace must have at least 3 segments:  ' . $options['namespace']);
        }

        if (count($segments) == 3) {
            $model_type = ucfirst(strtolower($segments[1]));
            $model_name = ucfirst(strtolower($segments[2]));
        } else {
            $model_type = ucfirst(strtolower($segments[2]));
            $model_name = ucfirst(strtolower($segments[3]));
        }

        if (substr($model_name, strlen($model_name) - 4, 4) == '.xml') {
            $model_name = substr($model_name, 0, strlen($model_name) - 4); //remove .xml
        }

        if (file_exists($located_path)) {
        } else {
            throw new RuntimeException
            ('Resource XmlHandler located_path not found: ' . $this->resource_namespace);
        }

        try {
            $contents = file_get_contents($located_path);

            $scheme     = strtolower(trim($scheme));
            $model_type = ucfirst(strtolower(trim($model_type)));

            if ($scheme == 'query') {
                $xml = simplexml_load_string($contents);
                return $this->model_handler->getConfiguration($model_type, $model_name, $xml);

            } elseif ($model_type == 'Application') {
                $xml = simplexml_load_string($contents);
                return $xml;

            } elseif ($model_type == 'Fields' || $model_type == 'Include') {
                return $contents;

            } elseif ($model_type == 'Dataobject') {
                $xml = simplexml_load_string($contents);
                return $this->dataobject_handler->getConfiguration($model_type, $model_name, $xml);

            } else {
                $xml = simplexml_load_string($contents);
                return $this->model_handler->getConfiguration($model_type, $model_name, $xml);
            }
        } catch (Exception $e) {

            throw new RuntimeException
            ('Resource XmlHandler Failure:  ' . $located_path . ' ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a collection of a specific handler
     *
     * @param   string $scheme
     * @param   array  $options
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getCollection($scheme, array $options = array())
    {
        return null;
    }
}
