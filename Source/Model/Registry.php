<?php
/**
 * Model Registry Query Builder
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

use CommonApi\Query\QueryInterface2;
use CommonApi\Query\ModelRegistryInterface2;

/**
 * Model Registry Query Builder
 *
 * Base - Query - Filters - Utilities - Defaults - Columns - Criteria - Registry
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Registry extends Criteria implements ModelRegistryInterface2
{
    /**
     * Class Constructor
     *
     * @param  QueryInterface2 $query
     * @param  array           $model_registry
     *
     * @since  1.0.0
     */
    public function __construct(
        QueryInterface2 $query,
        $model_registry
    ) {
        parent::__construct($query, $model_registry);
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
        $this->setSelectColumns();
        $this->setFrom();
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
}
