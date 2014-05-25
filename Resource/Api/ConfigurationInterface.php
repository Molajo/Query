<?php
/**
 * Configuration Interface
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Api;

/**
 * Configuration Interface
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
interface ConfigurationInterface
{
    /**
     * Load registry for requested model resource, returning name of registry collection
     *
     * @param   string $model_type
     * @param   string $model_name
     * @param   object $xml
     *
     * @return  string
     * @since   1.0
     */
    public function getConfiguration($model_type, $model_name, $xml);

    /**
     * Store Configuration Data in Registry
     *
     * @param   string $model_registry
     * @param   object $xml
     *
     * @return  bool
     * @since   1.0
     */
    public function setModelRegistry($model_registry, $xml);
}
