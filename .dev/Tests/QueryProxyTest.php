<?php
/**
 * Query Proxy Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

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
     * @covers  Molajo\Query\Adapter\Mysql::__construct
     * @covers  Molajo\Query\Adapter\Postgresql::__construct
     * @covers  Molajo\Query\Adapter\Sqllite::__construct
     * @covers  Molajo\Query\Adapter\Sqlserver::__construct
     *
     * @covers  Molajo\Query\Builder\Sql::__construct
     * @covers  Molajo\Query\Builder\Sql::clearQuery
     * @covers  Molajo\Query\Builder\Generate::__construct
     * @covers  Molajo\Query\Builder\Groups::__construct
     * @covers  Molajo\Query\Builder\Collect::__construct
     * @covers  Molajo\Query\Builder\Edits::__construct
     * @covers  Molajo\Query\Builder\Filter::__construct
     * @covers  Molajo\Query\Builder\Base::__construct
     *
     * @return  \CommonApi\Query\QueryInterface
     * @since   1.0
     */
    public function setup()
    {
        $fieldhandler = new Fieldhandler();
        $database_prefix = 'molajo_';
        $database = new Database();

        $query_class = new QueryClass($fieldhandler, $database_prefix, $database);

        $this->query_proxy = new QueryProxy($query_class);
    }

    /**
     * Test setDateProperties
     *
     * @covers  Molajo\Query\Model::__construct
     * @covers  Molajo\Query\Model::setDateProperties
     *
     * @return void
     * @since   1.0
     */
    public function testConstruct()
    {
        return;
    }
}
