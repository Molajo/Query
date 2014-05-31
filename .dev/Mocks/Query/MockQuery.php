<?php
/**
 * Mock Query
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use CommonApi\Query\QueryInterface;

/**
 * Class Mock ModelRegistry
 *
 * Generates a registry for testing when used with the web and ModelRegistryQuery
 *
 * @package Molajo\Controller
 */
class MockQuery implements QueryInterface
{
    public function get($key, $default = null)
    {

    }

    public function clearQuery()
    {

    }

    public function setType($query_type = 'select')
    {

    }

    public function getDateFormat()
    {

    }

    public function getDate()
    {

    }

    public function getNullDate()
    {

    }

    public function setDistinct($distinct = false)
    {

    }

    public function select($column_name, $alias = null, $value = null, $data_type = null)
    {

    }

    public function from($table_name, $alias = null)
    {

    }

    public function whereGroup($group, $group_connector = 'and')
    {

    }

    public function where(
        $left_filter = 'column',
        $left = '',
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = null
    ) {
    }

    public function groupBy($column_name)
    {

    }

    public function havingGroup($group, $group_connector = 'and')
    {

    }

    public function having(
        $left_filter = 'column',
        $left = '',
        $condition = '=',
        $right_filter = 'column',
        $right = '',
        $connector = 'AND',
        $group = null
    ) {

    }

    public function orderBy($column_name, $direction = 'ASC')
    {

    }

    public function setOffsetAndLimit($offset = 0, $limit = 15)
    {

    }

    public function getSql($sql = null)
    {

    }
}

