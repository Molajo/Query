<?php
/**
 * Model Registry Query Builder
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

use CommonApi\Query\QueryInterface;
use CommonApi\Query\QueryBuilderInterface;

/**
 * Model Registry Query Builder
 *
 * Registry->Criteria->Columns->Table->Defaults->Utilities->Query->Base
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Registry extends Criteria implements QueryBuilderInterface
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
        $model_registry
    ) {
        parent::__construct($query, $model_registry);
    }

    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  $this
     * @since   1.0.0
     */
    public function set($key, $value = null)
    {
        $this->$key = $value;

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
        } else {
            return parent::getSql($sql);
        }

        $this->setModelRegistrySQL();

        return $this->query->getSql();
    }

    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function getModelRegistry($key = null, $default = null)
    {
        if ($key === null) {
            return $this->model_registry;
        }

        return $this->getModelRegistryByKey($key, $default);
    }

    /**
     * Get the value of a specified Model Registry Key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setModelRegistry($key = null, $value = null)
    {
        if ($key === null) {
            $this->model_registry = $value;
        }

        $this->model_registry[$key] = $value;

        return $this;
    }

    /**
     * Replace the Model Registry
     *
     * @param   mixed $value
     *
     * @return  $this
     * @since   1.0.0
     */
    public function replaceModelRegistry($value = null)
    {
//        if (is_array($value)) {
//        } else {
//            $value = array();
//        }

        $this->model_registry = $value;

//        $this->initialiseModelRegistry();

        return $this;
    }

    /**
     * Build SQL from Model Registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelRegistrySQL()
    {
        if (count($this->model_registry) === 0) {
            return $this;
        }

        $this->setSelectColumns();
        $this->setFrom();
        $this->setJoins();
        $this->setKeyCriteria();
        $this->setModelCriteria();
        $this->setModelCriteriaArrayCriteria();

        $this->use_pagination = $this->getModelRegistry('use_pagination');
        $this->offset         = $this->getModelRegistry('offset');
        $this->count          = $this->getModelRegistry('count');

        return $this;
    }
}
