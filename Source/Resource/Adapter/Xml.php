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
class Xml extends ConfigurationFactory implements ResourceInterface
{
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
        list($model_type, $model_name) = $this->setModelTypeName($segments);
        $contents = $this->readXmlFile($located_path);

        return $this->handlePathResults($model_type, $model_name, $contents);
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
     * @return  array
     * @since   1.0.0
     */
    protected function setModelTypeName(array $segments = array())
    {
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

        return array($model_type, $model_name);
    }

    /**
     * Read xml file
     *
     * @param   string $located_path
     *
     * @return  object
     * @since   1.0.0
     */
    protected function readXmlFile($located_path)
    {
        if (file_exists($located_path)) {
        } else {
            return '';
        }

        return file_get_contents($located_path);
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
    protected function handlePathApplication($contents)
    {
        $xml = simplexml_load_string($contents);

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
