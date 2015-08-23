<?php
/**
 * Query Builder Edits
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

use CommonApi\Exception\RuntimeException;

/**
 * Query Builder Edits
 *
 * Sql - BuildSql - BuildSqlElements - BuildSqlGroups - SetData - EditData - FilterData - Base
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class EditData extends FilterData
{
    /**
     * Edit Array
     *
     * @param   mixed   $array
     * @param   string  $type
     * @param   boolean $exception
     *
     * @return  array
     * @since   1.0.0
     */
    protected function editArray($array, $type = 'columns', $exception = true)
    {
        if (is_array($array) && count($array) > 0) {
            return $array;
        }

        if ($exception === true) {
            throw new RuntimeException('editArray Method: ' . $type . ' does not have data.');
        }

        return array();
    }

    /**
     * Tests if a required value has been provided
     *
     * @param   string $data_type
     * @param   string $column_name
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function editDataType($data_type = null, $column_name = '')
    {
        if ($data_type === null) {
            throw new RuntimeException(
                'Query-editDataType Method: No Datatype provided for Column: ' . $column_name
            );
        }
    }

    /**
     * Tests if a required value has been provided
     *
     * @param   mixed  $column_name
     * @param   string $value
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function editRequired($column_name, $value = null)
    {
        if (is_array($value)) {
            return $value;
        }

        if (trim($value) === '' || $value === null) {
            throw new RuntimeException('Query: Value required for: ' . $column_name);
        }

        return $value;
    }

    /**
     * Edit Connector
     *
     * @param   string $connector
     *
     * @return  string
     * @since   1.0.0
     */
    protected function editConnector($connector = null)
    {
        $connector = strtoupper($connector);

        if (in_array($connector, $this->connector)) {
        } else {
            $connector = 'AND';
        }

        return $connector;
    }

    /**
     * Edit WHERE
     *
     * @param   string $left
     * @param   string $condition
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function editWhere($left, $condition)
    {
        if (trim($left) === ''
            || trim($condition) === ''
        ) {
            throw new RuntimeException(
                'Query-Where Method: Value required for ' . ' $left: ' . $left
                . ' $condition: ' . $condition
            );
        }

        return $this;
    }
}
