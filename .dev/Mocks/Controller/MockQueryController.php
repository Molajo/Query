<?php
/**
 * Mock Controller
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Query\ControllerInterface;
use CommonApi\Query\ModelInterface;
use CommonApi\Query\QueryInterface;

/**
 * Query Controller Proxy
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class MockQueryController implements QueryInterface
{
    protected $columns;
    protected $null_date;
    protected $current_date;

    public function __construct(
        array $columns = array()
    ) {
        $this->columns = $columns;

        $this->setDateProperties();
    }

    /**
     * Set Default Values for SQL
     *
     * @param   string $null_date
     * @param   string $current_date
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setDateProperties()
    {
        $this->null_date    = '0000-00-00 00:00:00';
        $this->current_date = '2013-06-01 00:00:00';

        return $this;
    }

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
        $condition = '',
        $right_filter = 'column',
        $right = '',
        $connector = 'and',
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
        $condition = '',
        $right_filter = 'column',
        $right = '',
        $connector = 'and',
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
