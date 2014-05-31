<?php
/**
 * Model Registry Columns
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\ModelRegistry;

/**
 * Model Registry Columns
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Columns extends Criteria
{
    /**
     * Create "select columns" statements when no columns have been specified
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSelectColumns()
    {
        if ($this->useSelectColumns() === false) {
            return $this;
        }

        if ($this->model_registry['query_object'] === 'result') {
            return $this->setSelectColumnsResultQuery();
        }

        if ($this->model_registry['query_object'] === 'distinct') {
            return $this->setSelectColumnsDistinctQuery();
        }

        $this->setSelectColumnsModelRegistry();

        return $this;
    }

    /**
     * Should Columns be used for Select List?
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useSelectColumns()
    {
        if (count($this->get('columns')) === 0) {
            return true;
        }

        return false;
    }

    /**
     * Create "select columns" statements: Result Query
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSelectColumnsResultQuery()
    {
        if ((int)$this->model_registry['primary_key_value'] > 0) {
            $this->setSelect($this->model_registry['primary_prefix'] . '.' . $this->model_registry['name_key']);

            return $this;
        }

        $this->setSelect($this->model_registry['primary_prefix'] . '.' . $this->model_registry['primary_key']);

        return $this;
    }

    /**
     * Create "select columns" statements: Distinct Query
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSelectColumnsDistinctQuery()
    {
        $this->setSelect($this->model_registry['primary_prefix'] . '.' . $this->model_registry['primary_key']);

        $this->setSelect($this->model_registry['primary_prefix'] . '.' . $this->model_registry['name_key']);

        return $this;
    }

    /**
     * Create "select columns" statements: Model Registry Columns
     *
     * @return  $this
     * @since   1.0
     */
    protected function setSelectColumnsModelRegistry()
    {
        if (count($this->model_registry['fields']) === 0) {
            $this->setSelect($this->model_registry['primary_prefix'] . '.' . '*');

        } else {
            foreach ($this->model_registry['fields'] as $column) {
                $this->setSelect($this->model_registry['primary_prefix'] . '.' . $column['name']);
            }
        }

        return $this;
    }

    /**
     * II. FROM TABLE
     *
     * Set From Table Value
     *
     * @return  $this
     * @since   1.0
     */
    protected function setFromTable()
    {
        if ($this->useFromTable() === false) {
            return $this;
        }

        $primary_prefix = $this->model_registry['primary_prefix'];
        $table_name     = $this->model_registry['table_name'];

        $this->setFrom($table_name, $primary_prefix);

        return $this;
    }

    /**
     * Determine whether or not to use the From Table Value
     *
     * @return  boolean
     * @since   1.0
     */
    protected function useFromTable()
    {
        if (count($this->get('from', array())) > 0) {
            return false;
        }

        return true;
    }
}
