<?php
/**
 * Xml Handler
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Adapter;

use CommonApi\Resource\ResourceInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Xml Handler
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
final class Xml extends ConfigurationFactory implements ResourceInterface
{
    /**
     * Default Xml
     *
     * @var    object
     * @since  1.0.0
     */
    protected $default_xml;

    /**
     * Extension Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $extension_type = null;

    /**
     * Extension Sub Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $extension_subtype = null;

    /**
     * Model Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_type = null;

    /**
     * Model Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_name = null;

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
        if (isset($handler_options['default_xml'])) {
            $this->default_xml = $handler_options['default_xml'];
            unset($handler_options['default_xml']);
        }

        parent::__construct(
            $base_path,
            $resource_map,
            $namespace_prefixes,
            $valid_file_extensions,
            $cache_callbacks,
            $handler_options
        );
    }

    /**
     * Xml file is located, read, loaded using simplexml into a string and then sent back
     *  or processed by the Configuration data_object or Model utility
     *
     * @param   string $scheme
     * @param   string $located_path
     * @param   array  $options
     *
     * @return  void|mixed
     * @since   1.0.0
     */
    public function handlePath($located_path, array $options = array())
    {
        $segments = $this->handlePathSegments($options);

        $this->setModelTypeName($segments);

        $contents = $this->readXmlFile($located_path);

        return $this->handlePathResults($this->model_type, $this->model_name, $contents);
    }

    /**
     * Retrieve a collection of a specific handler
     *
     * @param   string $scheme
     * @param   array  $options
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getCollection($scheme, array $options = array())
    {
        return null;
    }

    /**
     * Break Namespace into Segments
     *
     * @param   array $options
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function handlePathSegments(array $options = array())
    {
        $segments = explode('//', $options['resource_namespace']);

        if (count($segments) > 3) {
        } else {
            echo '<pre>';
            var_dump($segments);

            throw new RuntimeException(
                'Resource XmlHandler Failure namespace must have at least 3 segments:  '
                . $options['namespace']
            );
        }

        return $segments;
    }

    /**
     * Derive Model Type and Name from NS Segments
     *
     * @param   array $segments
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelTypeName(array $segments = array())
    {
        $this->extension_type = ucfirst(strtolower($segments[2]));

        if (ucfirst(strtolower($segments[2])) === 'Resources') {
            $model_type = $segments[2];
            $model_name = $segments[3] . $segments[4];

        } elseif (count($segments) === 4) {
            $model_type = $segments[2];
            $model_name = $segments[3];

        } else {
            $model_type = $segments[3];
            $model_name = $segments[4];
        }

        if (substr(strtolower($model_name), strlen($model_name) - 4, 4) === '.xml') {
            $model_name = substr($model_name, 0, strlen($model_name) - 4); //remove .xml
        }

        return $this->setModelTypeNameCase($model_type, $model_name);
    }

    /**
     * Derive Model Type and Name from NS Segments
     *
     * @param   string $model_type
     * @param   string $model_name
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setModelTypeNameCase($model_type, $model_name)
    {
        $model_type = ucfirst(strtolower(trim($model_type)));
        $model_name = ucfirst(strtolower(trim($model_name)));

        $this->model_type = $model_type;
        $this->model_name = $model_name;

        return $this;
    }

    /**
     * Read xml file
     *
     * @param   string $located_path
     *
     * @return  string
     * @since   1.0.0
     */
    protected function readXmlFile($located_path)
    {
        if (file_exists($located_path)) {
            return file_get_contents($located_path);
        }

        if (is_object($this->default_xml)) {
            return $this->default_xml->get(
                $this->resource_namespace,
                $this->extension_type,
                $this->model_type,
                $this->model_name
            );
        }

        throw new RuntimeException('Resource Xml Adapter: No file found for: ' . $this->resource_namespace);
    }

    /**
     * Process Request given path
     *
     * @param   string $model_type
     * @param   string $model_name
     * @param   string $contents
     *
     * @return  object
     * @since   1.0.0
     */
    protected function handlePathResults($model_type, $model_name, $contents)
    {
        if ($model_type === 'Application') {
            return $this->handlePathApplication($contents);
        }

        if ($model_type === 'Include') {
            return $this->handlePathInclude($contents);
        }

        return $this->handlePathDataConfiguration($model_type, $model_name, $contents);
    }

    /**
     * Process Application Request
     *
     * @param   string $contents
     *
     * @return  object
     * @since   1.0.0
     */
    protected function handlePathApplication($file_location)
    {
        $xml = simplexml_load_string($file_location);

        return $xml;
    }

    /**
     * Process Include Request
     *
     * @param   string $contents
     *
     * @return  object
     * @since   1.0.0
     */
    protected function handlePathInclude($contents)
    {
        return $contents;
    }

    /**
     * Process data_object Request
     *
     * @param   string $model_type
     * @param   string $model_name
     * @param   string $contents
     *
     * @return  object
     * @since   1.0.0
     */
    protected function handlePathDataConfiguration($model_type, $model_name, $contents)
    {
        if ($model_type === 'Dataobject') {
            $method = 'instantiateDataObjectConfiguration';
        } else {
            $method = 'instantiateModelConfiguration';
        }

        $configuration = $this->$method();

        $xml = simplexml_load_string($contents);

        return $configuration->getConfiguration($model_type, $model_name, $xml);
    }
}
