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
     * @covers  Molajo\Query\Adapter\Mysql::__construct
     * @covers  Molajo\Query\Adapter\Postgresql::__construct
     * @covers  Molajo\Query\Adapter\Sqllite::__construct
     * @covers  Molajo\Query\Adapter\Sqlserver::__construct
     *
     * @covers  Molajo\Query\QueryProxy::__construct
     * @covers  Molajo\Query\QueryProxy::getSql
     * @covers  Molajo\Query\QueryProxy::clearQuery
     * @covers  Molajo\Query\QueryProxy::setType
     * @covers  Molajo\Query\QueryProxy::getDateFormat
     * @covers  Molajo\Query\QueryProxy::getDate
     * @covers  Molajo\Query\QueryProxy::getNullDate
     * @covers  Molajo\Query\QueryProxy::setDistinct
     * @covers  Molajo\Query\QueryProxy::select
     * @covers  Molajo\Query\QueryProxy::from
     * @covers  Molajo\Query\QueryProxy::whereGroup
     * @covers  Molajo\Query\QueryProxy::where
     * @covers  Molajo\Query\QueryProxy::groupBy
     * @covers  Molajo\Query\QueryProxy::havingGroup
     * @covers  Molajo\Query\QueryProxy::having
     * @covers  Molajo\Query\QueryProxy::orderBy
     * @covers  Molajo\Query\QueryProxy::setOffsetAndLimit
     * @covers  Molajo\Query\QueryProxy::get
     *
     * @covers  Molajo\Query\Builder\Sql::__construct
     * @covers  Molajo\Query\Builder\Sql::get
     * @covers  Molajo\Query\Builder\Sql::clearQuery
     * @covers  Molajo\Query\Builder\Sql::setType
     * @covers  Molajo\Query\Builder\Sql::getDate
     * @covers  Molajo\Query\Builder\Sql::getNullDate
     * @covers  Molajo\Query\Builder\Sql::getDateFormat
     * @covers  Molajo\Query\Builder\Sql::setDistinct
     * @covers  Molajo\Query\Builder\Sql::select
     * @covers  Molajo\Query\Builder\Sql::from
     * @covers  Molajo\Query\Builder\Sql::whereGroup
     * @covers  Molajo\Query\Builder\Sql::havingGroup
     * @covers  Molajo\Query\Builder\Sql::where
     * @covers  Molajo\Query\Builder\Sql::having
     * @covers  Molajo\Query\Builder\Sql::groupBy
     * @covers  Molajo\Query\Builder\Sql::orderBy
     * @covers  Molajo\Query\Builder\Sql::setOffsetAndLimit
     * @covers  Molajo\Query\Builder\Sql::getSql
     * @covers  Molajo\Query\Builder\Generate::getExternalSql
     * @covers  Molajo\Query\Builder\Generate::generateSql
     * @covers  Molajo\Query\Builder\Generate::getInsert
     * @covers  Molajo\Query\Builder\Generate::getInsertColumnsValues
     * @covers  Molajo\Query\Builder\Generate::getInsertfrom
     * @covers  Molajo\Query\Builder\Generate::getUpdate
     * @covers  Molajo\Query\Builder\Generate::getDelete
     * @covers  Molajo\Query\Builder\Generate::getSelect
     * @covers  Molajo\Query\Builder\Generate::getDistinct
     * @covers  Molajo\Query\Builder\Generate::getColumns
     * @covers  Molajo\Query\Builder\Generate::getFrom
     * @covers  Molajo\Query\Builder\Generate::getWhere
     * @covers  Molajo\Query\Builder\Generate::getHaving
     * @covers  Molajo\Query\Builder\Generate::getElement
     * @covers  Molajo\Query\Builder\Generate::getLimit
     * @covers  Molajo\Query\Builder\Generate::getDatabasePrefix
     * @covers  Molajo\Query\Builder\Groups::setGroup
     * @covers  Molajo\Query\Builder\Groups::getGroups
     * @covers  Molajo\Query\Builder\Groups::initialiseGroups
     * @covers  Molajo\Query\Builder\Groups::getGroupItemsLoop
     * @covers  Molajo\Query\Builder\Groups::getGroupItem
     * @covers  Molajo\Query\Builder\Groups::getLoop
     * @covers  Molajo\Query\Builder\Groups::getLoopList
     * @covers  Molajo\Query\Builder\Elements::getElementsArray
     * @covers  Molajo\Query\Builder\Elements::getElementValues
     * @covers  Molajo\Query\Builder\Elements::getElementArrayEntry
     * @covers  Molajo\Query\Builder\Elements::setColumnValue
     * @covers  Molajo\Query\Builder\Elements::setOrFilterColumn
     * @covers  Molajo\Query\Builder\Elements::setColumnName
     * @covers  Molajo\Query\Builder\Elements::setColumnAlias
     * @covers  Molajo\Query\Builder\Elements::setLeftRightConditionals
     * @covers  Molajo\Query\Builder\Elements::setGroupByOrderBy
     * @covers  Molajo\Query\Builder\Elements::setDirection
     * @covers  Molajo\Query\Builder\Elements::setOffsetorLimit
     * @covers  Molajo\Query\Builder\Item::setItem
     * @covers  Molajo\Query\Builder\Item::setItemValue
     * @covers  Molajo\Query\Builder\Item::setItemAlias
     * @covers  Molajo\Query\Builder\Item::setItemName
     * @covers  Molajo\Query\Builder\Item::setItemDataType
     * @covers  Molajo\Query\Builder\Item::setItemValueInDataType
     * @covers  Molajo\Query\Builder\Edits::editArray
     * @covers  Molajo\Query\Builder\Edits::editDataType
     * @covers  Molajo\Query\Builder\Edits::editRequired
     * @covers  Molajo\Query\Builder\Edits::editConnector
     * @covers  Molajo\Query\Builder\Edits::editWhere
     * @covers  Molajo\Query\Builder\Filters::quoteValue
     * @covers  Molajo\Query\Builder\Filters::quoteNameAndPrefix
     * @covers  Molajo\Query\Builder\Filters::quoteName
     * @covers  Molajo\Query\Builder\Filters::filter
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
     * Test setDateProperties
     *
     * @covers  Molajo\Query\Adapter\Mysql::__construct
     * @covers  Molajo\Query\Adapter\Postgresql::__construct
     * @covers  Molajo\Query\Adapter\Sqllite::__construct
     * @covers  Molajo\Query\Adapter\Sqlserver::__construct
     *
     * @covers  Molajo\Query\QueryProxy::__construct
     * @covers  Molajo\Query\QueryProxy::getSql
     * @covers  Molajo\Query\QueryProxy::clearQuery
     * @covers  Molajo\Query\QueryProxy::setType
     * @covers  Molajo\Query\QueryProxy::getDateFormat
     * @covers  Molajo\Query\QueryProxy::getDate
     * @covers  Molajo\Query\QueryProxy::getNullDate
     * @covers  Molajo\Query\QueryProxy::setDistinct
     * @covers  Molajo\Query\QueryProxy::select
     * @covers  Molajo\Query\QueryProxy::from
     * @covers  Molajo\Query\QueryProxy::whereGroup
     * @covers  Molajo\Query\QueryProxy::where
     * @covers  Molajo\Query\QueryProxy::groupBy
     * @covers  Molajo\Query\QueryProxy::havingGroup
     * @covers  Molajo\Query\QueryProxy::having
     * @covers  Molajo\Query\QueryProxy::orderBy
     * @covers  Molajo\Query\QueryProxy::setOffsetAndLimit
     * @covers  Molajo\Query\QueryProxy::get
     *
     * @covers  Molajo\Query\Builder\Sql::__construct
     * @covers  Molajo\Query\Builder\Sql::get
     * @covers  Molajo\Query\Builder\Sql::clearQuery
     * @covers  Molajo\Query\Builder\Sql::setType
     * @covers  Molajo\Query\Builder\Sql::getDate
     * @covers  Molajo\Query\Builder\Sql::getNullDate
     * @covers  Molajo\Query\Builder\Sql::getDateFormat
     * @covers  Molajo\Query\Builder\Sql::setDistinct
     * @covers  Molajo\Query\Builder\Sql::select
     * @covers  Molajo\Query\Builder\Sql::from
     * @covers  Molajo\Query\Builder\Sql::whereGroup
     * @covers  Molajo\Query\Builder\Sql::havingGroup
     * @covers  Molajo\Query\Builder\Sql::where
     * @covers  Molajo\Query\Builder\Sql::having
     * @covers  Molajo\Query\Builder\Sql::groupBy
     * @covers  Molajo\Query\Builder\Sql::orderBy
     * @covers  Molajo\Query\Builder\Sql::setOffsetAndLimit
     * @covers  Molajo\Query\Builder\Sql::getSql
     * @covers  Molajo\Query\Builder\Generate::getExternalSql
     * @covers  Molajo\Query\Builder\Generate::generateSql
     * @covers  Molajo\Query\Builder\Generate::getInsert
     * @covers  Molajo\Query\Builder\Generate::getInsertColumnsValues
     * @covers  Molajo\Query\Builder\Generate::getInsertfrom
     * @covers  Molajo\Query\Builder\Generate::getUpdate
     * @covers  Molajo\Query\Builder\Generate::getDelete
     * @covers  Molajo\Query\Builder\Generate::getSelect
     * @covers  Molajo\Query\Builder\Generate::getDistinct
     * @covers  Molajo\Query\Builder\Generate::getColumns
     * @covers  Molajo\Query\Builder\Generate::getFrom
     * @covers  Molajo\Query\Builder\Generate::getWhere
     * @covers  Molajo\Query\Builder\Generate::getHaving
     * @covers  Molajo\Query\Builder\Generate::getElement
     * @covers  Molajo\Query\Builder\Generate::getLimit
     * @covers  Molajo\Query\Builder\Generate::getDatabasePrefix
     * @covers  Molajo\Query\Builder\Groups::setGroup
     * @covers  Molajo\Query\Builder\Groups::getGroups
     * @covers  Molajo\Query\Builder\Groups::initialiseGroups
     * @covers  Molajo\Query\Builder\Groups::getGroupItemsLoop
     * @covers  Molajo\Query\Builder\Groups::getGroupItem
     * @covers  Molajo\Query\Builder\Groups::getLoop
     * @covers  Molajo\Query\Builder\Groups::getLoopList
     * @covers  Molajo\Query\Builder\Elements::getElementsArray
     * @covers  Molajo\Query\Builder\Elements::getElementValues
     * @covers  Molajo\Query\Builder\Elements::getElementArrayEntry
     * @covers  Molajo\Query\Builder\Elements::setColumnValue
     * @covers  Molajo\Query\Builder\Elements::setOrFilterColumn
     * @covers  Molajo\Query\Builder\Elements::setColumnName
     * @covers  Molajo\Query\Builder\Elements::setColumnAlias
     * @covers  Molajo\Query\Builder\Elements::setLeftRightConditionals
     * @covers  Molajo\Query\Builder\Elements::setGroupByOrderBy
     * @covers  Molajo\Query\Builder\Elements::setDirection
     * @covers  Molajo\Query\Builder\Elements::setOffsetorLimit
     * @covers  Molajo\Query\Builder\Item::setItem
     * @covers  Molajo\Query\Builder\Item::setItemValue
     * @covers  Molajo\Query\Builder\Item::setItemAlias
     * @covers  Molajo\Query\Builder\Item::setItemName
     * @covers  Molajo\Query\Builder\Item::setItemDataType
     * @covers  Molajo\Query\Builder\Item::setItemValueInDataType
     * @covers  Molajo\Query\Builder\Edits::editArray
     * @covers  Molajo\Query\Builder\Edits::editDataType
     * @covers  Molajo\Query\Builder\Edits::editRequired
     * @covers  Molajo\Query\Builder\Edits::editConnector
     * @covers  Molajo\Query\Builder\Edits::editWhere
     * @covers  Molajo\Query\Builder\Filters::quoteValue
     * @covers  Molajo\Query\Builder\Filters::quoteNameAndPrefix
     * @covers  Molajo\Query\Builder\Filters::quoteName
     * @covers  Molajo\Query\Builder\Filters::filter
     *
     * @return void
     * @since   1.0
     */
    public function testConstruct()
    {
        $this->query_proxy->setType('select');
        $this->query_proxy->setDistinct(true);
        $this->query_proxy->select('application_id');
        $this->query_proxy->select('catalog_type_id');
        $this->query_proxy->from('#__catalog_types');
//$this->query_proxy->where('column', 'enabled', '=', 'integer', 1);
//        $this->query_proxy->groupBy('catalog_type_id');
//        $this->query_proxy->orderBy('order_id');

        $this->assertEquals('select', $this->query_proxy->get('query_type'));

        echo $this->query_proxy->getSQL();
    }

    /**
     * Test setDateProperties
     *
     * @covers  Molajo\Query\Adapter\Mysql::__construct
     * @covers  Molajo\Query\Adapter\Postgresql::__construct
     * @covers  Molajo\Query\Adapter\Sqllite::__construct
     * @covers  Molajo\Query\Adapter\Sqlserver::__construct
     *
     * @covers  Molajo\Query\QueryProxy::__construct
     * @covers  Molajo\Query\QueryProxy::getSql
     * @covers  Molajo\Query\QueryProxy::clearQuery
     * @covers  Molajo\Query\QueryProxy::setType
     * @covers  Molajo\Query\QueryProxy::getDateFormat
     * @covers  Molajo\Query\QueryProxy::getDate
     * @covers  Molajo\Query\QueryProxy::getNullDate
     * @covers  Molajo\Query\QueryProxy::setDistinct
     * @covers  Molajo\Query\QueryProxy::select
     * @covers  Molajo\Query\QueryProxy::from
     * @covers  Molajo\Query\QueryProxy::whereGroup
     * @covers  Molajo\Query\QueryProxy::where
     * @covers  Molajo\Query\QueryProxy::groupBy
     * @covers  Molajo\Query\QueryProxy::havingGroup
     * @covers  Molajo\Query\QueryProxy::having
     * @covers  Molajo\Query\QueryProxy::orderBy
     * @covers  Molajo\Query\QueryProxy::setOffsetAndLimit
     * @covers  Molajo\Query\QueryProxy::get
     *
     * @covers  Molajo\Query\Builder\Sql::__construct
     * @covers  Molajo\Query\Builder\Sql::get
     * @covers  Molajo\Query\Builder\Sql::clearQuery
     * @covers  Molajo\Query\Builder\Sql::setType
     * @covers  Molajo\Query\Builder\Sql::getDate
     * @covers  Molajo\Query\Builder\Sql::getNullDate
     * @covers  Molajo\Query\Builder\Sql::getDateFormat
     * @covers  Molajo\Query\Builder\Sql::setDistinct
     * @covers  Molajo\Query\Builder\Sql::select
     * @covers  Molajo\Query\Builder\Sql::from
     * @covers  Molajo\Query\Builder\Sql::whereGroup
     * @covers  Molajo\Query\Builder\Sql::havingGroup
     * @covers  Molajo\Query\Builder\Sql::where
     * @covers  Molajo\Query\Builder\Sql::having
     * @covers  Molajo\Query\Builder\Sql::groupBy
     * @covers  Molajo\Query\Builder\Sql::orderBy
     * @covers  Molajo\Query\Builder\Sql::setOffsetAndLimit
     * @covers  Molajo\Query\Builder\Sql::getSql
     * @covers  Molajo\Query\Builder\Generate::getExternalSql
     * @covers  Molajo\Query\Builder\Generate::generateSql
     * @covers  Molajo\Query\Builder\Generate::getInsert
     * @covers  Molajo\Query\Builder\Generate::getInsertColumnsValues
     * @covers  Molajo\Query\Builder\Generate::getInsertfrom
     * @covers  Molajo\Query\Builder\Generate::getUpdate
     * @covers  Molajo\Query\Builder\Generate::getDelete
     * @covers  Molajo\Query\Builder\Generate::getSelect
     * @covers  Molajo\Query\Builder\Generate::getDistinct
     * @covers  Molajo\Query\Builder\Generate::getColumns
     * @covers  Molajo\Query\Builder\Generate::getFrom
     * @covers  Molajo\Query\Builder\Generate::getWhere
     * @covers  Molajo\Query\Builder\Generate::getHaving
     * @covers  Molajo\Query\Builder\Generate::getElement
     * @covers  Molajo\Query\Builder\Generate::getLimit
     * @covers  Molajo\Query\Builder\Generate::getDatabasePrefix
     * @covers  Molajo\Query\Builder\Groups::setGroup
     * @covers  Molajo\Query\Builder\Groups::getGroups
     * @covers  Molajo\Query\Builder\Groups::initialiseGroups
     * @covers  Molajo\Query\Builder\Groups::getGroupItemsLoop
     * @covers  Molajo\Query\Builder\Groups::getGroupItem
     * @covers  Molajo\Query\Builder\Groups::getLoop
     * @covers  Molajo\Query\Builder\Groups::getLoopList
     * @covers  Molajo\Query\Builder\Elements::getElementsArray
     * @covers  Molajo\Query\Builder\Elements::getElementValues
     * @covers  Molajo\Query\Builder\Elements::getElementArrayEntry
     * @covers  Molajo\Query\Builder\Elements::setColumnValue
     * @covers  Molajo\Query\Builder\Elements::setOrFilterColumn
     * @covers  Molajo\Query\Builder\Elements::setColumnName
     * @covers  Molajo\Query\Builder\Elements::setColumnAlias
     * @covers  Molajo\Query\Builder\Elements::setLeftRightConditionals
     * @covers  Molajo\Query\Builder\Elements::setGroupByOrderBy
     * @covers  Molajo\Query\Builder\Elements::setDirection
     * @covers  Molajo\Query\Builder\Elements::setOffsetorLimit
     * @covers  Molajo\Query\Builder\Item::setItem
     * @covers  Molajo\Query\Builder\Item::setItemValue
     * @covers  Molajo\Query\Builder\Item::setItemAlias
     * @covers  Molajo\Query\Builder\Item::setItemName
     * @covers  Molajo\Query\Builder\Item::setItemDataType
     * @covers  Molajo\Query\Builder\Item::setItemValueInDataType
     * @covers  Molajo\Query\Builder\Edits::editArray
     * @covers  Molajo\Query\Builder\Edits::editDataType
     * @covers  Molajo\Query\Builder\Edits::editRequired
     * @covers  Molajo\Query\Builder\Edits::editConnector
     * @covers  Molajo\Query\Builder\Edits::editWhere
     * @covers  Molajo\Query\Builder\Filters::quoteValue
     * @covers  Molajo\Query\Builder\Filters::quoteNameAndPrefix
     * @covers  Molajo\Query\Builder\Filters::quoteName
     * @covers  Molajo\Query\Builder\Filters::filter
     *
     * @return void
     * @since   1.0
     */
    public function testDates()
    {
        $this->assertEquals(19, strlen($this->query_proxy->getDate()));
        $this->assertEquals('0000-00-00 00:00:00', $this->query_proxy->getNullDate());
        $this->assertEquals('Y-m-d H:i:s', $this->query_proxy->getDateFormat());

        $this->query_proxy->setDistinct(false);

        $this->assertEquals(false, $this->query_proxy->get('distinct'));
    }

    /**
     * Test setDateProperties
     *
     * @covers  Molajo\Query\Adapter\Mysql::__construct
     * @covers  Molajo\Query\Adapter\Postgresql::__construct
     * @covers  Molajo\Query\Adapter\Sqllite::__construct
     * @covers  Molajo\Query\Adapter\Sqlserver::__construct
     *
     * @covers  Molajo\Query\QueryProxy::__construct
     * @covers  Molajo\Query\QueryProxy::getSql
     * @covers  Molajo\Query\QueryProxy::clearQuery
     * @covers  Molajo\Query\QueryProxy::setType
     * @covers  Molajo\Query\QueryProxy::getDateFormat
     * @covers  Molajo\Query\QueryProxy::getDate
     * @covers  Molajo\Query\QueryProxy::getNullDate
     * @covers  Molajo\Query\QueryProxy::setDistinct
     * @covers  Molajo\Query\QueryProxy::select
     * @covers  Molajo\Query\QueryProxy::from
     * @covers  Molajo\Query\QueryProxy::whereGroup
     * @covers  Molajo\Query\QueryProxy::where
     * @covers  Molajo\Query\QueryProxy::groupBy
     * @covers  Molajo\Query\QueryProxy::havingGroup
     * @covers  Molajo\Query\QueryProxy::having
     * @covers  Molajo\Query\QueryProxy::orderBy
     * @covers  Molajo\Query\QueryProxy::setOffsetAndLimit
     * @covers  Molajo\Query\QueryProxy::get
     *
     * @covers  Molajo\Query\Builder\Sql::__construct
     * @covers  Molajo\Query\Builder\Sql::get
     * @covers  Molajo\Query\Builder\Sql::clearQuery
     * @covers  Molajo\Query\Builder\Sql::setType
     * @covers  Molajo\Query\Builder\Sql::getDate
     * @covers  Molajo\Query\Builder\Sql::getNullDate
     * @covers  Molajo\Query\Builder\Sql::getDateFormat
     * @covers  Molajo\Query\Builder\Sql::setDistinct
     * @covers  Molajo\Query\Builder\Sql::select
     * @covers  Molajo\Query\Builder\Sql::from
     * @covers  Molajo\Query\Builder\Sql::whereGroup
     * @covers  Molajo\Query\Builder\Sql::havingGroup
     * @covers  Molajo\Query\Builder\Sql::where
     * @covers  Molajo\Query\Builder\Sql::having
     * @covers  Molajo\Query\Builder\Sql::groupBy
     * @covers  Molajo\Query\Builder\Sql::orderBy
     * @covers  Molajo\Query\Builder\Sql::setOffsetAndLimit
     * @covers  Molajo\Query\Builder\Sql::getSql
     * @covers  Molajo\Query\Builder\Generate::getExternalSql
     * @covers  Molajo\Query\Builder\Generate::generateSql
     * @covers  Molajo\Query\Builder\Generate::getInsert
     * @covers  Molajo\Query\Builder\Generate::getInsertColumnsValues
     * @covers  Molajo\Query\Builder\Generate::getInsertfrom
     * @covers  Molajo\Query\Builder\Generate::getUpdate
     * @covers  Molajo\Query\Builder\Generate::getDelete
     * @covers  Molajo\Query\Builder\Generate::getSelect
     * @covers  Molajo\Query\Builder\Generate::getDistinct
     * @covers  Molajo\Query\Builder\Generate::getColumns
     * @covers  Molajo\Query\Builder\Generate::getFrom
     * @covers  Molajo\Query\Builder\Generate::getWhere
     * @covers  Molajo\Query\Builder\Generate::getHaving
     * @covers  Molajo\Query\Builder\Generate::getElement
     * @covers  Molajo\Query\Builder\Generate::getLimit
     * @covers  Molajo\Query\Builder\Generate::getDatabasePrefix
     * @covers  Molajo\Query\Builder\Groups::setGroup
     * @covers  Molajo\Query\Builder\Groups::getGroups
     * @covers  Molajo\Query\Builder\Groups::initialiseGroups
     * @covers  Molajo\Query\Builder\Groups::getGroupItemsLoop
     * @covers  Molajo\Query\Builder\Groups::getGroupItem
     * @covers  Molajo\Query\Builder\Groups::getLoop
     * @covers  Molajo\Query\Builder\Groups::getLoopList
     * @covers  Molajo\Query\Builder\Elements::getElementsArray
     * @covers  Molajo\Query\Builder\Elements::getElementValues
     * @covers  Molajo\Query\Builder\Elements::getElementArrayEntry
     * @covers  Molajo\Query\Builder\Elements::setColumnValue
     * @covers  Molajo\Query\Builder\Elements::setOrFilterColumn
     * @covers  Molajo\Query\Builder\Elements::setColumnName
     * @covers  Molajo\Query\Builder\Elements::setColumnAlias
     * @covers  Molajo\Query\Builder\Elements::setLeftRightConditionals
     * @covers  Molajo\Query\Builder\Elements::setGroupByOrderBy
     * @covers  Molajo\Query\Builder\Elements::setDirection
     * @covers  Molajo\Query\Builder\Elements::setOffsetorLimit
     * @covers  Molajo\Query\Builder\Item::setItem
     * @covers  Molajo\Query\Builder\Item::setItemValue
     * @covers  Molajo\Query\Builder\Item::setItemAlias
     * @covers  Molajo\Query\Builder\Item::setItemName
     * @covers  Molajo\Query\Builder\Item::setItemDataType
     * @covers  Molajo\Query\Builder\Item::setItemValueInDataType
     * @covers  Molajo\Query\Builder\Edits::editArray
     * @covers  Molajo\Query\Builder\Edits::editDataType
     * @covers  Molajo\Query\Builder\Edits::editRequired
     * @covers  Molajo\Query\Builder\Edits::editConnector
     * @covers  Molajo\Query\Builder\Edits::editWhere
     * @covers  Molajo\Query\Builder\Filters::quoteValue
     * @covers  Molajo\Query\Builder\Filters::quoteNameAndPrefix
     * @covers  Molajo\Query\Builder\Filters::quoteName
     * @covers  Molajo\Query\Builder\Filters::filter
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
