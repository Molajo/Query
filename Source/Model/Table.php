<?php
/**
 * Model Registry Table
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

/**
 * Model Registry Table
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Table extends Columns
{
    /**
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

        $this->from($table_name, $primary_prefix);

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

        $this->setOffsetAndLimit($this->model_registry['offset'], $this->model_registry['limit']);

        return $this;
    }
}
