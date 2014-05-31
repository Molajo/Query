<?php
/**
 * Model Registry Query Builder
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\ModelRegistry;

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
class Builder extends Defaults implements ModelRegistryInterface
{
    /**
     * Class Constructor
     *
     * @param  QueryInterface $query
     * @param  array          $model_registry
     *
     * @since  1.0.0
     */
    public function __construct(
        QueryInterface $query,
        array $model_registry = array()
    ) {
        parent::__construct(
            $query
        );

        $this->setDateProperties();

        $this->setModelRegistryDefaults($model_registry);
    }

    /**
     * Build SQL from Model Registry
     *
     * @param   string $sql
     *
     * @return  string
     * @since   1.0.0
     */
    public function getSQL($sql = null)
    {
        if ($sql === null) {
            $this->setModelRegistrySQL();
        }

        return $sql;
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
     * Set Model Registry Limits
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistryLimits()
    {
        if (count($this->model_registry['use_pagination']) === 0) {
        } else {
            return $this;
        }

        $this->setOffsetAndLimit(
            $this->model_registry['offset'],
            $this->model_registry['limit']
        );

        return $this;
    }
}
