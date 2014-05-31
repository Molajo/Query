<?php
/**
 * Model Registry Query Builder
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

use CommonApi\Query\QueryInterface;
use CommonApi\Query\ModelRegistryInterface;

/**
 * Model Registry Query Builder
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Registry extends Criteria implements ModelRegistryInterface
{
    /**
     * Build SQL from Model Registry
     *
     * @param   string $sql
     *
     * @return  string
     * @since   1.0.0
     */
    public function getSql($sql = null)
    {
        if ($sql === null) {
            $this->setModelRegistrySQL();
        }

        return $sql;
    }

    /**
     * Build SQL from Model Registry
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistrySQL()
    {
        $this->setSelectDistinct();
        $this->setSelectColumns();
        $this->setFromTable();
        $this->setKeyCriteria();
        $this->setJoins();
        $this->setModelCriteria();
        $this->setModelCriteriaArrayCriteria();

        $this->query_object   = $this->getModelRegistry('query_object');
        $this->use_pagination = $this->getModelRegistry('use_pagination');
        $this->offset         = $this->getModelRegistry('offset');
        $this->count          = $this->getModelRegistry('count');

        return $this;
    }

    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function getModelRegistry($key = null, $default = null)
    {
        if ($key === '*' || trim($key) === '' || $key === null) {
            return $this->getModelRegistryAll();
        }

        return $this->getModelRegistryByKey($key, $default);
    }

    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  $this
     * @since   1.0
     */
    public function setModelRegistry($key, $value = null)
    {
        $this->model_registry[$key] = $value;

        return $this;
    }

    /**
     * Get the full contents of the Model Registry
     *
     * @return  mixed
     * @since   1.0
     */
    protected function getModelRegistryAll()
    {
        return $this->model_registry;
    }

    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     */
    protected function getModelRegistryByKey($key = null, $default = null)
    {
        if (isset($this->model_registry[$key])) {
        } else {
            $this->model_registry[$key] = $default;
        }

        return $this->model_registry[$key];
    }
}
