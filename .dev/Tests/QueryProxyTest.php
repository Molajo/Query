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
     * @covers  Molajo\Query\Builder\Generate::getExternalSql
     * @covers  Molajo\Query\Builder\Generate::generateSql
     * @covers  Molajo\Query\Builder\Generate::getInsert
     * @covers  Molajo\Query\Builder\Generate::getInsertColumnsValues
     * @covers  Molajo\Query\Builder\Generate::getInsertfrom
     * @covers  Molajo\Query\Builder\Generate::getUpdate
     * @covers  Molajo\Query\Builder\Generate::getDelete
     * @covers  Molajo\Query\Builder\Generate::getSelect
     * @covers  Molajo\Query\Builder\Generate::getDistinct
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
     * @covers  Molajo\Query\Builder\Elements::setLeftRightConditionalItem
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
     * @covers  Molajo\Query\Model\Registry::__construct
     * @covers  Molajo\Query\Model\Registry::getModelRegistry
     * @covers  Molajo\Query\Model\Registry::setModelRegistry
     * @covers  Molajo\Query\Model\Registry::getSql
     * @covers  Molajo\Query\Model\Registry::setModelRegistrySQL
     * @covers  Molajo\Query\Model\Criteria::setKeyCriteria
     * @covers  Molajo\Query\Model\Criteria::setWhereStatementsKeyValue
     * @covers  Molajo\Query\Model\Criteria::setModelCriteria
     * @covers  Molajo\Query\Model\Criteria::setModelCriteriaWhere
     * @covers  Molajo\Query\Model\Criteria::useModelCriteriaWhere
     * @covers  Molajo\Query\Model\Criteria::setModelCriteriaArrayCriteria
     * @covers  Molajo\Query\Model\Criteria::useModelCriteriaArray
     * @covers  Molajo\Query\Model\Criteria::setModelRegistryCriteriaArrayItem
     * @covers  Molajo\Query\Model\Columns::setSelectDistinct
     * @covers  Molajo\Query\Model\Columns::setSelectColumns
     * @covers  Molajo\Query\Model\Columns::useSelectColumns
     * @covers  Molajo\Query\Model\Columns::setSelectColumnsResultQuery
     * @covers  Molajo\Query\Model\Columns::setSelectColumnsDistinctQuery
     * @covers  Molajo\Query\Model\Columns::setSelectColumnsModelRegistry
     * @covers  Molajo\Query\Model\Table::setFrom
     * @covers  Molajo\Query\Model\Table::useFromTable
     * @covers  Molajo\Query\Model\Table::setJoins
     * @covers  Molajo\Query\Model\Table::useJoins
     * @covers  Molajo\Query\Model\Table::setJoinItem
     * @covers  Molajo\Query\Model\Table::setJoinItemColumns
     * @covers  Molajo\Query\Model\Table::useJoinItemColumns
     * @covers  Molajo\Query\Model\Table::setJoinItemWhere
     * @covers  Molajo\Query\Model\Table::setJoinItemWhereLoop
     * @covers  Molajo\Query\Model\Table::useJoinItemWhere
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaults
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryBase
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Query\Model\Defaults::getModelRegistryPrimaryKeyValue
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryPrimaryKeyValue
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Query\Model\Defaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Query\Model\Utilities::getModelRegistryAll
     * @covers  Molajo\Query\Model\Utilities::getModelRegistryByKey
     * @covers  Molajo\Query\Model\Utilities::setProperty
     * @covers  Molajo\Query\Model\Utilities::setPropertyArray
     * @covers  Molajo\Query\Model\Utilities::verifyPropertyExists
     * @covers  Molajo\Query\Model\Utilities::setWherePair
     * @covers  Molajo\Query\Model\Utilities::setWhereOperator
     * @covers  Molajo\Query\Model\Utilities::setWhereElement
     * @covers  Molajo\Query\Model\Utilities::setWhereElementProperty
     * @covers  Molajo\Query\Model\Utilities::setWhereElementNumericValue
     * @covers  Molajo\Query\Model\Utilities::setWhereElementTableColumn
     * @covers  Molajo\Query\Model\Utilities::setModelRegistryLimits
     * @covers  Molajo\Query\Model\Query::setDateProperties
     * @covers  Molajo\Query\Model\Query::clearQuery
     * @covers  Molajo\Query\Model\Query::setType
     * @covers  Molajo\Query\Model\Query::getDateFormat
     * @covers  Molajo\Query\Model\Query::getDate
     * @covers  Molajo\Query\Model\Query::getNullDate
     * @covers  Molajo\Query\Model\Query::setDistinct
     * @covers  Molajo\Query\Model\Query::select
     * @covers  Molajo\Query\Model\Query::from
     * @covers  Molajo\Query\Model\Query::whereGroup
     * @covers  Molajo\Query\Model\Query::where
     * @covers  Molajo\Query\Model\Query::groupBy
     * @covers  Molajo\Query\Model\Query::havingGroup
     * @covers  Molajo\Query\Model\Query::having
     * @covers  Molajo\Query\Model\Query::orderBy
     * @covers  Molajo\Query\Model\Query::setOffsetAndLimit
     * @covers  Molajo\Query\Model\Query::get

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
     *
     * @return void
     * @since   1.0
     */
    public function testQueryProxy()
    {
        $columns_array = array();

        /** Columns */
        $row           = new \stdClass();
        $row->name      = 'application_id';
        $row->prefix    = 'a.';
        $row->data_type = 'integer';
        $row->value     = null;
        $row->alias     = 'app_id';

        $columns_array['a.application_id'] = $row;

        $row = new \stdClass();
        $row->name      = 'application_name';
        $row->prefix    = '';
        $row->data_type = 'string';
        $row->value     = null;
        $row->alias     = null;

        $columns_array[$row->name] = $row;

        /** From */
        $from_array = array();

        $row = new \stdClass();
        $row->name      = '#__catalog_types';
        $row->prefix    = '';
        $row->data_type = 'string';
        $row->value     = null;
        $row->alias     = 'a';

        $from_array[$row->name] = $row;

        /** Where */
        $where_array = array();

        $where = new \stdClass();

        $where->group = '';

        $left = new \stdClass();
        $left->name      = 'enabled';
        $left->prefix    = 'a.';
        $left->data_type = 'string';
        $left->value     = null;
        $left->alias     = null;

        $where->left_item = $left;

        $where->condition = '=';

        $right = new \stdClass();
        $right->name      = '1';
        $right->prefix    = '';
        $right->data_type = 'integer';
        $right->value     = "'1'";
        $right->alias     = null;

        $where->right_item = $right;

        $where->connector = 'AND';

        $where_array[] = $where;

        $where = new \stdClass();

        $where->group = '';

        $left = new \stdClass();
        $left->name      = 'dog';
        $left->prefix    = 'a.';
        $left->data_type = 'string';
        $left->value     = null;
        $left->alias     = null;

        $where->left_item = $left;

        $where->condition = '=';

        $right = new \stdClass();
        $right->name      = 'barks';
        $right->prefix    = '';
        $right->data_type = 'string';
        $right->value     = "'barks'";
        $right->alias     = null;

        $where->right_item = $right;

        $where->connector = 'AND';

        $where_array[] = $where;


        /** Group By */
        $group_by_array = array();

        $row = new \stdClass();
        $row->name      = 'catalog_type_id';
        $row->prefix    = '';

        $group_by_array[] = $row;

        /** Order By */
        $order_by_array = array();

        $row = new \stdClass();
        $row->name      = 'order_id';
        $row->prefix    = '';
        $row->direction = 'ASC';

        $order_by_array[] = $row;

        $row = new \stdClass();
        $row->name      = 'line2';
        $row->prefix    = '';
        $row->direction = 'DESC';

        $order_by_array[] = $row;

        /** Having */
        $having_array = array();

        $having = new \stdClass();

        $having->group = '';

        $left = new \stdClass();
        $left->name      = 'status';
        $left->prefix    = '';
        $left->data_type = 'string';
        $left->value     = null;
        $left->alias     = null;

        $having->left_item = $left;

        $having->condition = '>=';

        $right = new \stdClass();
        $right->name      = '3';
        $right->prefix    = '';
        $right->data_type = 'integer';
        $right->value     = "'3'";
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
        file_put_contents(__DIR__ . '/testQueryProxy.txt', $sql);

        $this->assertEquals(file_get_contents(__DIR__ . '/testQueryProxy.txt'), $sql);
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
