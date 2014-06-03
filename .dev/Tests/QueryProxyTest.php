<?php
/**
 * Query Proxy Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use ReflectionClass;
use Molajo\Fieldhandler\MockRequest as Fieldhandler;
use Molajo\Database\MockDatabase as Database;
use Molajo\Query\Adapter\Mysql as QueryClass;

use PHPUnit_Framework_TestCase;

/**
 * Query Proxy Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class QueryProxyTest extends PHPUnit_Framework_TestCase
{
    protected $query_proxy;

    /**
     * Setup
     *
     * @return  \CommonApi\Query\QueryInterface
     * @since   1.0
     */
    public function setup()
    {
        $fieldhandler    = new Fieldhandler();
        $database_prefix = 'molajo_';
        $database        = new Database();

        $query_class = new QueryClass($fieldhandler, $database_prefix, $database);

        $this->query_proxy = new QueryProxy($query_class);
    }

    /**
     * Test All Properties
     *
     * @return void
     * @since   1.0
     */
    public function testQueryProxy()
    {
        $columns_array = array();

        /** Columns */
        $row            = new \stdClass();
        $row->name      = 'application_id';
        $row->prefix    = 'a';
        $row->data_type = 'string';
        $row->value     = 'integer';
        $row->alias     = 'app_id';

        $columns_array['a.application_id'] = $row;

        $row            = new \stdClass();
        $row->name      = 'application_name';
        $row->prefix    = '';
        $row->data_type = 'string';
        $row->value     = 'string';
        $row->alias     = null;

        $columns_array[$row->name] = $row;

        /** From */
        $from_array = array();

        $row            = new \stdClass();
        $row->name      = '#__catalog_types';
        $row->prefix    = '';
        $row->data_type = 'string';
        $row->value     = null;
        $row->alias     = 'a';
        $row->primary   = true;

        $from_array[$row->name] = $row;

        /** Where */
        $where_array = array();

        $where = new \stdClass();

        $where->group = '';

        $left            = new \stdClass();
        $left->name      = 'enabled';
        $left->prefix    = 'a';
        $left->data_type = 'column';
        $left->value     = null;
        $left->alias     = null;

        $where->left_item = $left;

        $where->condition = '=';

        $right            = new \stdClass();
        $right->name      = '1';
        $right->prefix    = '';
        $right->data_type = 'integer';
        $right->value     = 1;
        $right->alias     = null;

        $where->right_item = $right;

        $where->connector = 'AND';

        $where_array[] = $where;

        $where = new \stdClass();

        $where->group = '';

        $left            = new \stdClass();
        $left->name      = 'dog';
        $left->prefix    = 'a';
        $left->data_type = 'column';
        $left->value     = null;
        $left->alias     = null;

        $where->left_item = $left;

        $where->condition = '=';

        $right            = new \stdClass();
        $right->name      = 'barks';
        $right->prefix    = '';
        $right->data_type = 'string';
        $right->value     = 'barks';
        $right->alias     = null;

        $where->right_item = $right;

        $where->connector = 'AND';

        $where_array[] = $where;


        /** Group By */
        $group_by_array = array();

        $row            = new \stdClass();
        $row->name      = 'catalog_type_id';
        $row->prefix    = '';
        $row->data_type = 'column';
        $row->value     = null;
        $row->alias     = null;

        $group_by_array[] = $row;

        /** Order By */
        $order_by_array = array();

        $row            = new \stdClass();
        $row->name      = 'order_id';
        $row->prefix    = '';
        $row->data_type = 'column';
        $row->value     = null;
        $row->alias     = null;
        $row->direction = 'ASC';

        $order_by_array[] = $row;

        $row            = new \stdClass();
        $row->name      = 'line2';
        $row->prefix    = '';
        $row->data_type = 'column';
        $row->value     = null;
        $row->alias     = null;
        $row->direction = 'DESC';

        $order_by_array[] = $row;

        /** Having */
        $having_array = array();

        $having = new \stdClass();

        $having->group = '';

        $left            = new \stdClass();
        $left->name      = 'status';
        $left->prefix    = '';
        $left->data_type = 'column';
        $left->value     = null;
        $left->alias     = null;

        $having->left_item = $left;

        $having->condition = '>=';

        $right            = new \stdClass();
        $right->name      = '3';
        $right->prefix    = '';
        $right->data_type = 'integer';
        $right->value     = 3;
        $right->alias     = null;

        $having->right_item = $right;

        $having->connector = 'AND';

        $having_array[] = $having;

        $this->query_proxy->setType('select');
        $this->query_proxy->setDistinct(true);
        $this->query_proxy->select('a.application_id', 'app_id', 'integer');
        $this->query_proxy->select('application_name', '', 'string');
        $this->query_proxy->from('#__catalog_types', 'a');
        $this->query_proxy->where('column', 'a.enabled', '=', 'integer', 1);
        $this->query_proxy->where('column', 'a.dog', '=', 'string', 'barks');
        $this->query_proxy->groupBy('catalog_type_id');
        $this->query_proxy->orderBy('order_id');
        $this->query_proxy->orderBy('line2', 'DESC');
        $this->query_proxy->having('column', 'status', '>=', 'integer', 3);
        $this->query_proxy->setOffsetAndLimit(10, 5);
        $this->assertEquals('select', $this->query_proxy->get('query_type'));
        $this->assertEquals(true, $this->query_proxy->get('distinct'));
        $this->assertEquals($columns_array, $this->query_proxy->get('columns'));
        $this->assertEquals($from_array, $this->query_proxy->get('from'));
        $this->assertEquals($where_array, $this->query_proxy->get('where'));
        $this->assertEquals($having_array, $this->query_proxy->get('having'));
        $this->assertEquals($group_by_array, $this->query_proxy->get('group_by'));
        $this->assertEquals($order_by_array, $this->query_proxy->get('order_by'));
        $this->assertEquals(10, $this->query_proxy->get('offset'));
        $this->assertEquals(5, $this->query_proxy->get('limit'));

        $sql = $this->query_proxy->getSql();
//file_put_contents(__DIR__ . '/testQueryProxy.txt', $sql);

        $this->assertEquals(file_get_contents(__DIR__ . '/testQueryProxy.txt'), $sql);
    }

    /**
     * Test All Properties
     *
     * @return void
     * @since   1.0
     */
    public function testMinimal()
    {
        $this->query_proxy->select('a.application_id', 'app_id', 'integer');
        $this->query_proxy->from('#__catalog_types', 'a');

        $sql = $this->query_proxy->getSql();
//        file_put_contents(__DIR__ . '/testQueryProxyMinimal.txt', $sql);

        $this->assertEquals(file_get_contents(__DIR__ . '/testQueryProxyMinimal.txt'), $sql);
    }

    /**
     * Test setDateProperties
     *
     * @return void
     * @since   1.0
     */
    public function testDates()
    {
        $this->assertEquals(19, strlen($this->query_proxy->getDate()));
        $this->assertEquals('0000-00-00 00:00:00', $this->query_proxy->getNullDate());
        $this->assertEquals('Y-m-d H:i:s', $this->query_proxy->getDateFormat());
    }

    /**
     * Test setDateProperties
     *
     * @return void
     * @since   1.0
     */
    public function testDistinct()
    {
        $this->query_proxy->setDistinct(false);
        $this->assertEquals(false, $this->query_proxy->get('distinct'));

        $this->query_proxy->setDistinct(true);
        $this->assertEquals(true, $this->query_proxy->get('distinct'));
    }
}
