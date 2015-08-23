<?php
/**
 * Query Resource Handler
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Adapter;

use CommonApi\Resource\ResourceInterface;

/**
 * Query Resource Handler - Instantiates Model and Controller
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Query extends QueryFactory implements ResourceInterface
{
    /**
     * Handle requires located file
     *
     * @param   string $located_path
     * @param   array  $options
     *
     * @return  void|mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function handlePath($located_path, array $options = array())
    {
        $this->setModelRegistry($options);

        $options = $this->setQueryOptions($options);

        if (isset($options['crud_type'])) {
            $crud_type = ucfirst(strtolower(trim($options['crud_type'])));
        } else {
            $crud_type = 'Read';
        }

        $model = $this->createModel($crud_type)->instantiateClass();

        return $this->createController($crud_type, $model)->instantiateClass();
    }

    /**
     * Set Model Registry
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setModelRegistry(array $options = array())
    {
        $resource_namespace    = substr($options['resource_namespace'], strlen('query://'), 9999);
        $get_resource_callback = $this->get_resource_callback;
        $this->model_registry  = $get_resource_callback('xml://' . $resource_namespace, $options);

        return $this;
    }

    /**
     * Set Query Options
     *
     * @param   array $options
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setQueryOptions(array $options)
    {
        if (isset($options['sql'])) {
            $this->sql = $options['sql'];
        }

        if (isset($options['runtime_data'])) {
            $this->runtime_data = $options['runtime_data'];
        }

        if (isset($this->model_registry['model_offset'])) {
        } else {
            $this->model_registry['model_offset'] = 0;
        }

        if (isset($this->model_registry['model_count'])) {
        } else {
            $this->model_registry['model_count'] = 20;
        }

        if (isset($this->model_registry['use_pagination'])) {
        } else {
            $this->model_registry['use_pagination'] = 1;
        }

        return $options;
    }
}
