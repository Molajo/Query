<?php
/**
 * Query Proxy Test
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use Molajo\Fieldhandler\MockRequest as Fieldhandler;
use Molajo\Data\MockDatabase as Database;
use Molajo\Query\Adapter\Mysql as QueryClass;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Query Proxy Test
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class QueryProxyTest extends PHPUnit_Framework_TestCase
{
    protected $query_proxy;

    /**
     * Setup
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
     * @covers  Molajo\Query\Builder\Sql::getSql
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
     * @covers  Molajo\Query\Builder\BuildSql::getExternalSql
     * @covers  Molajo\Query\Builder\BuildSql::generateSql
     * @covers  Molajo\Query\Builder\BuildSql::getInsert
     * @covers  Molajo\Query\Builder\BuildSql::getInsertfrom
     * @covers  Molajo\Query\Builder\BuildSql::getInsertType
     * @covers  Molajo\Query\Builder\BuildSql::getUpdate
     * @covers  Molajo\Query\Builder\BuildSql::getDelete
     * @covers  Molajo\Query\Builder\BuildSql::getSelect
     * @covers  Molajo\Query\Builder\BuildSql::getSelectAppend
     * @covers  Molajo\Query\Builder\BuildSql::getDistinct
     * @covers  Molajo\Query\Builder\BuildSql::getDatabasePrefix
     * @covers  Molajo\Query\Builder\BuildSql::setOffsetOrLimit
     * @covers  Molajo\Query\Builder\BuildSql::setFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::findFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::initialiseFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::resetFromPrimary
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementStandard
     * @covers  Molajo\Query\Builder\BuildSqlElements::useGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementLimit
     * @covers  Molajo\Query\Builder\BuildSqlElements::returnGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArray
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getPrimaryColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesColumnName
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesValue
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnAlias
     * @covers  Molajo\Query\Builder\BuildSqlGroups::setGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupsBeforeAfter
     * @covers  Molajo\Query\Builder\BuildSqlGroups::initialiseGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItemsLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItem
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteLeftRight
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteList
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoopList
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionals
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionalItem
     * @covers  Molajo\Query\Builder\SetData::setGroupByOrderBy
     * @covers  Molajo\Query\Builder\SetData::setDirection
     * @covers  Molajo\Query\Builder\SetData::setItem
     * @covers  Molajo\Query\Builder\SetData::setItemValue
     * @covers  Molajo\Query\Builder\SetData::setItemAlias
     * @covers  Molajo\Query\Builder\SetData::setItemName
     * @covers  Molajo\Query\Builder\SetData::setItemDataType
     * @covers  Molajo\Query\Builder\SetData::setItemValueInDataType
     * @covers  Molajo\Query\Builder\EditData::editArray
     * @covers  Molajo\Query\Builder\EditData::editDataType
     * @covers  Molajo\Query\Builder\EditData::editRequired
     * @covers  Molajo\Query\Builder\EditData::editConnector
     * @covers  Molajo\Query\Builder\EditData::editWhere
     * @covers  Molajo\Query\Builder\FilterData::quoteValue
     * @covers  Molajo\Query\Builder\FilterData::quoteNameAndPrefix
     * @covers  Molajo\Query\Builder\FilterData::quoteName
     * @covers  Molajo\Query\Builder\FilterData::filter
     *
     * @return  \CommonApi\Query\QueryInterface
     * @since   1.0.0
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
     * @covers  Molajo\Query\Builder\Sql::getSql
     * @covers  Molajo\Query\Builder\Sql::get
     * @covers  Molajo\Query\Builder\Sql::clearQuery
     * @covers  Molajo\Query\Builder\Sql::setType
     * @covers  Molajo\Query\Builder\Sql::getDate
     * @covers  Molajo\Query\Builder\Sql::getNullDate
     * @covers  Molajo\Query\Builder\Sql::getDateFormat
     * @covers  Molajo\Query\Builder\Sql::setDistinct
     * @covers  Molajo\Query\Builder\Sql::select
     * @covers  Molajo\Query\Builder\Sql::from
     * @covers  Molajo\Query\Builder\Sql::setFromKey
     * @covers  Molajo\Query\Builder\Sql::whereGroup
     * @covers  Molajo\Query\Builder\Sql::havingGroup
     * @covers  Molajo\Query\Builder\Sql::where
     * @covers  Molajo\Query\Builder\Sql::having
     * @covers  Molajo\Query\Builder\Sql::groupBy
     * @covers  Molajo\Query\Builder\Sql::orderBy
     * @covers  Molajo\Query\Builder\Sql::setOffsetAndLimit
     * @covers  Molajo\Query\Builder\BuildSql::getExternalSql
     * @covers  Molajo\Query\Builder\BuildSql::generateSql
     * @covers  Molajo\Query\Builder\BuildSql::getInsert
     * @covers  Molajo\Query\Builder\BuildSql::getInsertfrom
     * @covers  Molajo\Query\Builder\BuildSql::getInsertType
     * @covers  Molajo\Query\Builder\BuildSql::getUpdate
     * @covers  Molajo\Query\Builder\BuildSql::getDelete
     * @covers  Molajo\Query\Builder\BuildSql::getSelect
     * @covers  Molajo\Query\Builder\BuildSql::getSelectAppend
     * @covers  Molajo\Query\Builder\BuildSql::getDistinct
     * @covers  Molajo\Query\Builder\BuildSql::getDatabasePrefix
     * @covers  Molajo\Query\Builder\BuildSql::setOffsetOrLimit
     * @covers  Molajo\Query\Builder\BuildSql::setFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::findFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::initialiseFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::resetFromPrimary
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementStandard
     * @covers  Molajo\Query\Builder\BuildSqlElements::useGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementLimit
     * @covers  Molajo\Query\Builder\BuildSqlElements::returnGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArray
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArrayItem
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArrayItemSpecial
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getPrimaryColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesColumnName
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesValue
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnAlias
     * @covers  Molajo\Query\Builder\BuildSqlGroups::setGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupsBeforeAfter
     * @covers  Molajo\Query\Builder\BuildSqlGroups::initialiseGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItemsLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItem
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteLeftRight
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteList
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoopList
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionals
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionalItem
     * @covers  Molajo\Query\Builder\SetData::setGroupByOrderBy
     * @covers  Molajo\Query\Builder\SetData::setDirection
     * @covers  Molajo\Query\Builder\SetData::setItem
     * @covers  Molajo\Query\Builder\SetData::setItemValue
     * @covers  Molajo\Query\Builder\SetData::setItemAlias
     * @covers  Molajo\Query\Builder\SetData::setItemName
     * @covers  Molajo\Query\Builder\SetData::setItemDataType
     * @covers  Molajo\Query\Builder\SetData::setItemValueInDataType
     * @covers  Molajo\Query\Builder\EditData::editArray
     * @covers  Molajo\Query\Builder\EditData::editDataType
     * @covers  Molajo\Query\Builder\EditData::editRequired
     * @covers  Molajo\Query\Builder\EditData::editConnector
     * @covers  Molajo\Query\Builder\EditData::editWhere
     * @covers  Molajo\Query\Builder\FilterData::quoteValue
     * @covers  Molajo\Query\Builder\FilterData::quoteNameAndPrefix
     * @covers  Molajo\Query\Builder\FilterData::quoteName
     * @covers  Molajo\Query\Builder\FilterData::filter
     *
     * @return void
     * @since   1.0.0
     */
    public function testQueryProxy()
    {
        $columns_array = array();

        /** Columns */
        $row            = new stdClass();
        $row->name      = 'application_id';
        $row->prefix    = 'a';
        $row->data_type = 'string';
        $row->value     = 'integer';
        $row->alias     = 'app_id';

        $columns_array['a.application_id'] = $row;

        $row            = new stdClass();
        $row->name      = 'application_name';
        $row->prefix    = '';
        $row->data_type = 'string';
        $row->value     = 'string';
        $row->alias     = null;

        $columns_array[$row->name] = $row;

        /** From */
        $from_array = array();

        $row            = new stdClass();
        $row->name      = '#__catalog_types';
        $row->prefix    = '';
        $row->data_type = 'string';
        $row->value     = null;
        $row->alias     = 'a';
        $row->primary   = true;

        $from_array['#__catalog_types a'] = $row;

        /** Where */
        $where_array = array();

        $where = new stdClass();

        $where->group = '';

        $left            = new stdClass();
        $left->name      = 'enabled';
        $left->prefix    = 'a';
        $left->data_type = 'column';
        $left->value     = null;
        $left->alias     = null;

        $where->left_item = $left;

        $where->condition = '=';

        $right            = new stdClass();
        $right->name      = '1';
        $right->prefix    = '';
        $right->data_type = 'integer';
        $right->value     = 1;
        $right->alias     = null;

        $where->right_item = $right;

        $where->connector = 'AND';

        $where_array[] = $where;

        $where = new stdClass();

        $where->group = '';

        $left            = new stdClass();
        $left->name      = 'dog';
        $left->prefix    = 'a';
        $left->data_type = 'column';
        $left->value     = null;
        $left->alias     = null;

        $where->left_item = $left;

        $where->condition = '=';

        $right            = new stdClass();
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

        $row            = new stdClass();
        $row->name      = 'catalog_type_id';
        $row->prefix    = '';
        $row->data_type = 'column';
        $row->value     = null;
        $row->alias     = null;

        $group_by_array[] = $row;

        /** Order By */
        $order_by_array = array();

        $row            = new stdClass();
        $row->name      = 'order_id';
        $row->prefix    = '';
        $row->data_type = 'column';
        $row->value     = null;
        $row->alias     = null;
        $row->direction = 'ASC';

        $order_by_array[] = $row;

        $row            = new stdClass();
        $row->name      = 'line2';
        $row->prefix    = '';
        $row->data_type = 'column';
        $row->value     = null;
        $row->alias     = null;
        $row->direction = 'DESC';

        $order_by_array[] = $row;

        /** Having */
        $having_array = array();

        $having = new stdClass();

        $having->group = '';

        $left            = new stdClass();
        $left->name      = 'status';
        $left->prefix    = '';
        $left->data_type = 'column';
        $left->value     = null;
        $left->alias     = null;

        $having->left_item = $left;

        $having->condition = '>=';

        $right            = new stdClass();
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
     * @covers  Molajo\Query\Builder\Sql::getSql
     * @covers  Molajo\Query\Builder\Sql::get
     * @covers  Molajo\Query\Builder\Sql::clearQuery
     * @covers  Molajo\Query\Builder\Sql::setType
     * @covers  Molajo\Query\Builder\Sql::getDate
     * @covers  Molajo\Query\Builder\Sql::getNullDate
     * @covers  Molajo\Query\Builder\Sql::getDateFormat
     * @covers  Molajo\Query\Builder\Sql::setDistinct
     * @covers  Molajo\Query\Builder\Sql::select
     * @covers  Molajo\Query\Builder\Sql::from
     * @covers  Molajo\Query\Builder\Sql::setFromKey
     * @covers  Molajo\Query\Builder\Sql::whereGroup
     * @covers  Molajo\Query\Builder\Sql::havingGroup
     * @covers  Molajo\Query\Builder\Sql::where
     * @covers  Molajo\Query\Builder\Sql::having
     * @covers  Molajo\Query\Builder\Sql::groupBy
     * @covers  Molajo\Query\Builder\Sql::orderBy
     * @covers  Molajo\Query\Builder\Sql::setOffsetAndLimit
     * @covers  Molajo\Query\Builder\BuildSql::getExternalSql
     * @covers  Molajo\Query\Builder\BuildSql::generateSql
     * @covers  Molajo\Query\Builder\BuildSql::getInsert
     * @covers  Molajo\Query\Builder\BuildSql::getInsertfrom
     * @covers  Molajo\Query\Builder\BuildSql::getInsertType
     * @covers  Molajo\Query\Builder\BuildSql::getUpdate
     * @covers  Molajo\Query\Builder\BuildSql::getDelete
     * @covers  Molajo\Query\Builder\BuildSql::getSelect
     * @covers  Molajo\Query\Builder\BuildSql::getSelectAppend
     * @covers  Molajo\Query\Builder\BuildSql::getDistinct
     * @covers  Molajo\Query\Builder\BuildSql::getDatabasePrefix
     * @covers  Molajo\Query\Builder\BuildSql::setOffsetOrLimit
     * @covers  Molajo\Query\Builder\BuildSql::setFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::findFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::initialiseFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::resetFromPrimary
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementStandard
     * @covers  Molajo\Query\Builder\BuildSqlElements::useGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementLimit
     * @covers  Molajo\Query\Builder\BuildSqlElements::returnGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArray
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArrayItem
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArrayItemSpecial
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getPrimaryColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesColumnName
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesValue
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnAlias
     * @covers  Molajo\Query\Builder\BuildSqlGroups::setGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupsBeforeAfter
     * @covers  Molajo\Query\Builder\BuildSqlGroups::initialiseGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItemsLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItem
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteLeftRight
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteList
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoopList
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionals
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionalItem
     * @covers  Molajo\Query\Builder\SetData::setGroupByOrderBy
     * @covers  Molajo\Query\Builder\SetData::setDirection
     * @covers  Molajo\Query\Builder\SetData::setItem
     * @covers  Molajo\Query\Builder\SetData::setItemValue
     * @covers  Molajo\Query\Builder\SetData::setItemAlias
     * @covers  Molajo\Query\Builder\SetData::setItemName
     * @covers  Molajo\Query\Builder\SetData::setItemDataType
     * @covers  Molajo\Query\Builder\SetData::setItemValueInDataType
     * @covers  Molajo\Query\Builder\EditData::editArray
     * @covers  Molajo\Query\Builder\EditData::editDataType
     * @covers  Molajo\Query\Builder\EditData::editRequired
     * @covers  Molajo\Query\Builder\EditData::editConnector
     * @covers  Molajo\Query\Builder\EditData::editWhere
     * @covers  Molajo\Query\Builder\FilterData::quoteValue
     * @covers  Molajo\Query\Builder\FilterData::quoteNameAndPrefix
     * @covers  Molajo\Query\Builder\FilterData::quoteName
     * @covers  Molajo\Query\Builder\FilterData::filter
     *
     * @return void
     * @since   1.0.0
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
     * @covers  Molajo\Query\Builder\Sql::getSql
     * @covers  Molajo\Query\Builder\Sql::get
     * @covers  Molajo\Query\Builder\Sql::clearQuery
     * @covers  Molajo\Query\Builder\Sql::setType
     * @covers  Molajo\Query\Builder\Sql::getDate
     * @covers  Molajo\Query\Builder\Sql::getNullDate
     * @covers  Molajo\Query\Builder\Sql::getDateFormat
     * @covers  Molajo\Query\Builder\Sql::setDistinct
     * @covers  Molajo\Query\Builder\Sql::select
     * @covers  Molajo\Query\Builder\Sql::from
     * @covers  Molajo\Query\Builder\Sql::setFromKey
     * @covers  Molajo\Query\Builder\Sql::whereGroup
     * @covers  Molajo\Query\Builder\Sql::havingGroup
     * @covers  Molajo\Query\Builder\Sql::where
     * @covers  Molajo\Query\Builder\Sql::having
     * @covers  Molajo\Query\Builder\Sql::groupBy
     * @covers  Molajo\Query\Builder\Sql::orderBy
     * @covers  Molajo\Query\Builder\Sql::setOffsetAndLimit
     * @covers  Molajo\Query\Builder\BuildSql::getExternalSql
     * @covers  Molajo\Query\Builder\BuildSql::generateSql
     * @covers  Molajo\Query\Builder\BuildSql::getInsert
     * @covers  Molajo\Query\Builder\BuildSql::getInsertfrom
     * @covers  Molajo\Query\Builder\BuildSql::getInsertType
     * @covers  Molajo\Query\Builder\BuildSql::getUpdate
     * @covers  Molajo\Query\Builder\BuildSql::getDelete
     * @covers  Molajo\Query\Builder\BuildSql::getSelect
     * @covers  Molajo\Query\Builder\BuildSql::getSelectAppend
     * @covers  Molajo\Query\Builder\BuildSql::getDistinct
     * @covers  Molajo\Query\Builder\BuildSql::getDatabasePrefix
     * @covers  Molajo\Query\Builder\BuildSql::setOffsetOrLimit
     * @covers  Molajo\Query\Builder\BuildSql::setFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::findFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::initialiseFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::resetFromPrimary
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementStandard
     * @covers  Molajo\Query\Builder\BuildSqlElements::useGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementLimit
     * @covers  Molajo\Query\Builder\BuildSqlElements::returnGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArray
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArrayItem
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArrayItemSpecial
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getPrimaryColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesColumnName
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesValue
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnAlias
     * @covers  Molajo\Query\Builder\BuildSqlGroups::setGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupsBeforeAfter
     * @covers  Molajo\Query\Builder\BuildSqlGroups::initialiseGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItemsLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItem
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteLeftRight
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteList
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoopList
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionals
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionalItem
     * @covers  Molajo\Query\Builder\SetData::setGroupByOrderBy
     * @covers  Molajo\Query\Builder\SetData::setDirection
     * @covers  Molajo\Query\Builder\SetData::setItem
     * @covers  Molajo\Query\Builder\SetData::setItemValue
     * @covers  Molajo\Query\Builder\SetData::setItemAlias
     * @covers  Molajo\Query\Builder\SetData::setItemName
     * @covers  Molajo\Query\Builder\SetData::setItemDataType
     * @covers  Molajo\Query\Builder\SetData::setItemValueInDataType
     * @covers  Molajo\Query\Builder\EditData::editArray
     * @covers  Molajo\Query\Builder\EditData::editDataType
     * @covers  Molajo\Query\Builder\EditData::editRequired
     * @covers  Molajo\Query\Builder\EditData::editConnector
     * @covers  Molajo\Query\Builder\EditData::editWhere
     * @covers  Molajo\Query\Builder\FilterData::quoteValue
     * @covers  Molajo\Query\Builder\FilterData::quoteNameAndPrefix
     * @covers  Molajo\Query\Builder\FilterData::quoteName
     * @covers  Molajo\Query\Builder\FilterData::filter
     *
     * @return void
     * @since   1.0.0
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
     * @covers  Molajo\Query\Builder\Sql::getSql
     * @covers  Molajo\Query\Builder\Sql::get
     * @covers  Molajo\Query\Builder\Sql::clearQuery
     * @covers  Molajo\Query\Builder\Sql::setType
     * @covers  Molajo\Query\Builder\Sql::getDate
     * @covers  Molajo\Query\Builder\Sql::getNullDate
     * @covers  Molajo\Query\Builder\Sql::getDateFormat
     * @covers  Molajo\Query\Builder\Sql::setDistinct
     * @covers  Molajo\Query\Builder\Sql::select
     * @covers  Molajo\Query\Builder\Sql::from
     * @covers  Molajo\Query\Builder\Sql::setFromKey
     * @covers  Molajo\Query\Builder\Sql::whereGroup
     * @covers  Molajo\Query\Builder\Sql::havingGroup
     * @covers  Molajo\Query\Builder\Sql::where
     * @covers  Molajo\Query\Builder\Sql::having
     * @covers  Molajo\Query\Builder\Sql::groupBy
     * @covers  Molajo\Query\Builder\Sql::orderBy
     * @covers  Molajo\Query\Builder\Sql::setOffsetAndLimit
     * @covers  Molajo\Query\Builder\BuildSql::getExternalSql
     * @covers  Molajo\Query\Builder\BuildSql::generateSql
     * @covers  Molajo\Query\Builder\BuildSql::getInsert
     * @covers  Molajo\Query\Builder\BuildSql::getInsertfrom
     * @covers  Molajo\Query\Builder\BuildSql::getInsertType
     * @covers  Molajo\Query\Builder\BuildSql::getUpdate
     * @covers  Molajo\Query\Builder\BuildSql::getDelete
     * @covers  Molajo\Query\Builder\BuildSql::getSelect
     * @covers  Molajo\Query\Builder\BuildSql::getSelectAppend
     * @covers  Molajo\Query\Builder\BuildSql::getDistinct
     * @covers  Molajo\Query\Builder\BuildSql::getDatabasePrefix
     * @covers  Molajo\Query\Builder\BuildSql::setOffsetOrLimit
     * @covers  Molajo\Query\Builder\BuildSql::setFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::findFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::initialiseFromPrimary
     * @covers  Molajo\Query\Builder\BuildSql::resetFromPrimary
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementStandard
     * @covers  Molajo\Query\Builder\BuildSqlElements::useGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementLimit
     * @covers  Molajo\Query\Builder\BuildSqlElements::returnGetElement
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArray
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArrayItem
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementsArrayItemSpecial
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getPrimaryColumnPrefix
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesColumnName
     * @covers  Molajo\Query\Builder\BuildSqlElements::getElementValuesValue
     * @covers  Molajo\Query\Builder\BuildSqlElements::setColumnAlias
     * @covers  Molajo\Query\Builder\BuildSqlGroups::setGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroup
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupsBeforeAfter
     * @covers  Molajo\Query\Builder\BuildSqlGroups::initialiseGroups
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItemsLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getGroupItem
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteLeftRight
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getQuoteList
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoop
     * @covers  Molajo\Query\Builder\BuildSqlGroups::getLoopList
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionals
     * @covers  Molajo\Query\Builder\SetData::setLeftRightConditionalItem
     * @covers  Molajo\Query\Builder\SetData::setGroupByOrderBy
     * @covers  Molajo\Query\Builder\SetData::setDirection
     * @covers  Molajo\Query\Builder\SetData::setItem
     * @covers  Molajo\Query\Builder\SetData::setItemValue
     * @covers  Molajo\Query\Builder\SetData::setItemAlias
     * @covers  Molajo\Query\Builder\SetData::setItemName
     * @covers  Molajo\Query\Builder\SetData::setItemDataType
     * @covers  Molajo\Query\Builder\SetData::setItemValueInDataType
     * @covers  Molajo\Query\Builder\EditData::editArray
     * @covers  Molajo\Query\Builder\EditData::editDataType
     * @covers  Molajo\Query\Builder\EditData::editRequired
     * @covers  Molajo\Query\Builder\EditData::editConnector
     * @covers  Molajo\Query\Builder\EditData::editWhere
     * @covers  Molajo\Query\Builder\FilterData::quoteValue
     * @covers  Molajo\Query\Builder\FilterData::quoteNameAndPrefix
     * @covers  Molajo\Query\Builder\FilterData::quoteName
     * @covers  Molajo\Query\Builder\FilterData::filter
     *
     * @return void
     * @since   1.0.0
     */
    public function testDistinct()
    {
        $this->query_proxy->setDistinct(false);
        $this->assertEquals(false, $this->query_proxy->get('distinct'));

        $this->query_proxy->setDistinct(true);
        $this->assertEquals(true, $this->query_proxy->get('distinct'));
    }
}
