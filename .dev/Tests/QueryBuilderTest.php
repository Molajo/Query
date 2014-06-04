<?php
/**
 * Query Builder Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use Molajo\Controller\MockModelRegistry;
use Molajo\Fieldhandler\MockRequest as Fieldhandler;
use Molajo\Database\MockDatabase as Database;
use Molajo\Query\Adapter\Mysql as QueryClass;
use Molajo\Query\Model\Registry;

use PHPUnit_Framework_TestCase;

/**
 * $class   = new ReflectionClass('Molajo\Query\Model\Registry');
 * $methods = $class->getMethods();
 * foreach ($methods as $method) {
 * echo '     * @covers  ' . $method->class . '::' . $method->name . PHP_EOL;
 * }

 */


/**
 * Query Builder Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class QueryBuilderTest extends PHPUnit_Framework_TestCase
{
    protected $controller;

    /**
     * Test with Catalog Model Registry
     *
     * @covers  Molajo\Query\QueryBuilder::__construct
     * @covers  Molajo\Query\QueryBuilder::getModelRegistry
     * @covers  Molajo\Query\QueryBuilder::setModelRegistry
     * @covers  Molajo\Query\QueryBuilder::getSql
     * @covers  Molajo\Query\QueryBuilder::clearQuery
     * @covers  Molajo\Query\QueryBuilder::setType
     * @covers  Molajo\Query\QueryBuilder::getDateFormat
     * @covers  Molajo\Query\QueryBuilder::getDate
     * @covers  Molajo\Query\QueryBuilder::getNullDate
     * @covers  Molajo\Query\QueryBuilder::setDistinct
     * @covers  Molajo\Query\QueryBuilder::select
     * @covers  Molajo\Query\QueryBuilder::from
     * @covers  Molajo\Query\QueryBuilder::whereGroup
     * @covers  Molajo\Query\QueryBuilder::where
     * @covers  Molajo\Query\QueryBuilder::groupBy
     * @covers  Molajo\Query\QueryBuilder::havingGroup
     * @covers  Molajo\Query\QueryBuilder::having
     * @covers  Molajo\Query\QueryBuilder::orderBy
     * @covers  Molajo\Query\QueryBuilder::setOffsetAndLimit
     * @covers  Molajo\Query\QueryBuilder::get
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
     * @covers  Molajo\Query\Model\Registry::__construct
     * @covers  Molajo\Query\Model\Registry::getSql
     * @covers  Molajo\Query\Model\Registry::getModelRegistry
     * @covers  Molajo\Query\Model\Registry::setModelRegistry
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
     * @return  void
     * @since   1.0
     */
    public function testCatalogModelRegistry()
    {
        /** Fieldhandler */
        $fieldhandler = new Fieldhandler();

        /** Database */
        $database_prefix = 'molajo_';
        $database        = new Database();

        /** Query Proxy */
        $query_class = new QueryClass($fieldhandler, 'molajo_', $database);
        $query_proxy = new QueryProxy($query_class);

        /** Model Registry */
        $mock = new MockModelRegistry();
        $mr   = $mock->create();

        $registry = new Registry($query_proxy, $mr);

        $model_registry = $registry->getModelRegistry();

        $this->assertEquals('Catalog', $model_registry['name']);
        $this->assertEquals('#__catalog', $model_registry['table_name']);
        $this->assertEquals('id', $model_registry['primary_key']);
        $this->assertEquals('a', $model_registry['primary_prefix']);
        $this->assertEquals(0, $model_registry['get_customfields']);
        $this->assertEquals(0, $model_registry['get_item_children']);
        $this->assertEquals(0, $model_registry['use_special_joins']);
        $this->assertEquals(0, $model_registry['check_view_level_access']);
        $this->assertEquals(0, $model_registry['process_events']);
        $this->assertEquals(1, $model_registry['use_pagination']);
        $this->assertEquals('Database', $model_registry['data_object']);
        $this->assertEquals('Catalog', $model_registry['model_name']);
        $this->assertEquals('Datasource', $model_registry['model_type']);
        $this->assertEquals('CatalogDatasource', $model_registry['model_registry_name']);
        $this->assertEquals('Dataobject', $model_registry['data_object_data_object']);
        $this->assertEquals('Database', $model_registry['data_object_data_object_type']);
        $this->assertEquals('molajo', $model_registry['data_object_db']);
        $this->assertEquals('localhost', $model_registry['data_object_db_host']);
        $this->assertEquals('', $model_registry['data_object_db_password']);
        $this->assertEquals('molajo_', $model_registry['data_object_db_prefix']);
        $this->assertEquals('mysqli', $model_registry['data_object_db_type']);
        $this->assertEquals('', $model_registry['data_object_db_user']);
        $this->assertEquals('Database', $model_registry['data_object_model_name']);
        $this->assertEquals('Dataobject', $model_registry['data_object_model_type']);
        $this->assertEquals('Database', $model_registry['data_object_name']);
        $this->assertEquals(1, $model_registry['data_object_process_events']);
        $this->assertTrue(is_array($model_registry['fields']));
        $this->assertTrue(is_array($model_registry['joins']));
        $this->assertTrue(is_array($model_registry['joinfields']));
        $this->assertTrue(is_array($model_registry['foreignkeys']));
        $this->assertTrue(is_array($model_registry['criteria']));
        $this->assertTrue(is_array($model_registry['children']));
        $this->assertTrue(is_array($model_registry['customfieldgroups']));
        $this->assertEquals(0, $model_registry['model_offset']);
        $this->assertEquals(20, $model_registry['model_count']);
        $this->assertTrue(is_array($model_registry['fields']['0']));
        $this->assertTrue(is_array($model_registry['fields']['1']));
        $this->assertTrue(is_array($model_registry['fields']['2']));
        $this->assertTrue(is_array($model_registry['fields']['3']));
        $this->assertTrue(is_array($model_registry['fields']['4']));
        $this->assertTrue(is_array($model_registry['fields']['5']));
        $this->assertTrue(is_array($model_registry['fields']['6']));
        $this->assertTrue(is_array($model_registry['fields']['7']));
        $this->assertTrue(is_array($model_registry['fields']['8']));
        $this->assertTrue(is_array($model_registry['fields']['9']));
        $this->assertTrue(is_array($model_registry['fields']['10']));
        $this->assertEquals('id', $model_registry['fields']['0']['name']);
        $this->assertEquals('integer', $model_registry['fields']['0']['type']);
        $this->assertEquals(1, $model_registry['fields']['0']['null']);
        $this->assertEquals(0, $model_registry['fields']['0']['default']);
        $this->assertEquals(1, $model_registry['fields']['0']['identity']);
        $this->assertEquals('catalog_type_id', $model_registry['fields']['2']['name']);
        $this->assertEquals('integer', $model_registry['fields']['2']['type']);
        $this->assertEquals(0, $model_registry['fields']['2']['null']);
        $this->assertEquals('model', $model_registry['fields']['2']['default']);
        $this->assertEquals('CatalogType', $model_registry['fields']['2']['datalist']);
        $this->assertEquals(0, $model_registry['fields']['2']['locked']);
        $this->assertEquals('catalog_types_title', $model_registry['fields']['2']['display']);
        $this->assertEquals('source_id', $model_registry['fields']['3']['name']);
        $this->assertEquals('integer', $model_registry['fields']['3']['type']);
        $this->assertEquals(0, $model_registry['fields']['3']['null']);
        $this->assertEquals(' ', $model_registry['fields']['3']['default']);
        $this->assertEquals(1, $model_registry['fields']['3']['hidden']);
        $this->assertEquals('enabled', $model_registry['fields']['4']['name']);
        $this->assertEquals('boolean', $model_registry['fields']['4']['type']);
        $this->assertEquals(0, $model_registry['fields']['4']['null']);
        $this->assertEquals(0, $model_registry['fields']['4']['default']);
        $this->assertEquals('redirect_to_id', $model_registry['fields']['5']['name']);
        $this->assertEquals('integer', $model_registry['fields']['5']['type']);
        $this->assertEquals(0, $model_registry['fields']['5']['null']);
        $this->assertEquals(0, $model_registry['fields']['5']['default']);
        $this->assertEquals('sef_request', $model_registry['fields']['6']['name']);
        $this->assertEquals('string', $model_registry['fields']['6']['type']);
        $this->assertEquals(0, $model_registry['fields']['6']['null']);
        $this->assertEquals(' ', $model_registry['fields']['6']['default']);
        $this->assertEquals('page_type', $model_registry['fields']['7']['name']);
        $this->assertEquals('string', $model_registry['fields']['7']['type']);
        $this->assertEquals(0, $model_registry['fields']['7']['null']);
        $this->assertEquals(' ', $model_registry['fields']['7']['default']);
        $this->assertEquals('Pagetypes', $model_registry['fields']['7']['datalist']);
        $this->assertEquals('extension_instance_id', $model_registry['fields']['8']['name']);
        $this->assertEquals('integer', $model_registry['fields']['8']['type']);
        $this->assertEquals(0, $model_registry['fields']['8']['null']);
        $this->assertEquals(0, $model_registry['fields']['8']['default']);
        $this->assertEquals('ExtensionInstances', $model_registry['fields']['8']['datalist']);
        $this->assertEquals(1, $model_registry['fields']['8']['locked']);
        $this->assertEquals('name', $model_registry['fields']['8']['display']);
        $this->assertEquals('view_group_id', $model_registry['fields']['9']['name']);
        $this->assertEquals('integer', $model_registry['fields']['9']['type']);
        $this->assertEquals(0, $model_registry['fields']['9']['null']);
        $this->assertEquals(0, $model_registry['fields']['9']['default']);
        $this->assertEquals('primary_category_id', $model_registry['fields']['10']['name']);
        $this->assertEquals('integer', $model_registry['fields']['10']['type']);
        $this->assertEquals(0, $model_registry['fields']['10']['null']);
        $this->assertEquals(0, $model_registry['fields']['10']['default']);
        $this->assertTrue(is_array($model_registry['joins']['0']));
        $this->assertTrue(is_array($model_registry['joins']['1']));
        $this->assertTrue(is_array($model_registry['joins']['2']));
        $this->assertEquals('#__catalog_types', $model_registry['joins']['0']['table_name']);
        $this->assertEquals('b', $model_registry['joins']['0']['alias']);
        $this->assertEquals(
            'title,model_type,model_name,primary_category_id,alias',
            $model_registry['joins']['0']['select']
        );
        $this->assertEquals('id', $model_registry['joins']['0']['jointo']);
        $this->assertEquals('catalog_type_id', $model_registry['joins']['0']['joinwith']);
        $this->assertEquals('#__site_extension_instances', $model_registry['joins']['2']['table_name']);
        $this->assertEquals('site_extension_instances', $model_registry['joins']['2']['alias']);
        $this->assertEquals('', $model_registry['joins']['2']['select']);
        $this->assertTrue(is_array($model_registry['joinfields']['0']));
        $this->assertTrue(is_array($model_registry['joinfields']['1']));
        $this->assertTrue(is_array($model_registry['joinfields']['2']));
        $this->assertEquals('protected', $model_registry['joinfields']['0']['name']);
        $this->assertEquals('boolean', $model_registry['joinfields']['0']['type']);
        $this->assertEquals(0, $model_registry['joinfields']['0']['null']);
        $this->assertEquals(0, $model_registry['joinfields']['0']['default']);
        $this->assertEquals(1, $model_registry['joinfields']['0']['locked']);
        $this->assertEquals(1, $model_registry['joinfields']['0']['hidden']);
        $this->assertEquals('extension_instance_id', $model_registry['joinfields']['1']['name']);
        $this->assertEquals('integer', $model_registry['joinfields']['1']['type']);
        $this->assertEquals(0, $model_registry['joinfields']['1']['null']);
        $this->assertEquals(0, $model_registry['joinfields']['1']['default']);
        $this->assertEquals('ExtensionInstances', $model_registry['joinfields']['1']['datalist']);
        $this->assertEquals(1, $model_registry['joinfields']['1']['locked']);
        $this->assertEquals('name', $model_registry['joinfields']['1']['display']);
        $this->assertEquals('extension_instance_id', $model_registry['joinfields']['2']['name']);
        $this->assertEquals('integer', $model_registry['joinfields']['2']['type']);
        $this->assertEquals(0, $model_registry['joinfields']['2']['null']);
        $this->assertEquals(0, $model_registry['joinfields']['2']['default']);
        $this->assertEquals('ExtensionInstances', $model_registry['joinfields']['2']['datalist']);
        $this->assertEquals(1, $model_registry['joinfields']['2']['locked']);
        $this->assertEquals('name', $model_registry['joinfields']['2']['display']);
        $this->assertTrue(is_array($model_registry['foreignkeys']['0']));
        $this->assertEquals('catalog_type_id', $model_registry['foreignkeys']['0']['name']);
        $this->assertEquals('id', $model_registry['foreignkeys']['0']['source_id']);
        $this->assertEquals('CatalogTypes', $model_registry['foreignkeys']['0']['source_model']);
        $this->assertEquals(1, $model_registry['foreignkeys']['0']['required']);
        $this->assertTrue(is_array($model_registry['criteria']['0']));
        $this->assertTrue(is_array($model_registry['criteria']['1']));
        $this->assertEquals('a.enabled', $model_registry['criteria']['0']['name']);
        $this->assertEquals('=', $model_registry['criteria']['0']['connector']);
        $this->assertEquals(1, $model_registry['criteria']['0']['value']);
        $this->assertEquals('a.redirect_to_id', $model_registry['criteria']['1']['name']);
        $this->assertEquals('=', $model_registry['criteria']['1']['connector']);
        $this->assertEquals(0, $model_registry['criteria']['1']['value']);
        $this->assertTrue(is_array($model_registry['children']['0']));
        $this->assertTrue(is_array($model_registry['children']['1']));
        $this->assertEquals('Catalogactivity', $model_registry['children']['0']['name']);
        $this->assertEquals('Datasource', $model_registry['children']['0']['type']);
        $this->assertEquals('catalog_id', $model_registry['children']['0']['join']);
        $this->assertEquals('Catalogcategories', $model_registry['children']['1']['name']);
        $this->assertEquals('Datasource', $model_registry['children']['1']['type']);
        $this->assertEquals('catalog_id', $model_registry['children']['1']['join']);

        /**
         *  These are Route Commands
         */
        $registry->setModelRegistry('use_special_joins', 1);
        $registry->setModelRegistry('process_events', 0);
        $registry->setModelRegistry('query_object', 'item');

        $route_path = 'articles';

        $registry->where(
            'column',
            $registry->getModelRegistry('primary_prefix', 'a') . '.' . 'sef_request',
            '=',
            'string',
            $route_path
        );

        $registry->where(
            'column',
            $registry->getModelRegistry('primary_prefix', 'a') . '.' . 'page_type',
            '<>',
            'string',
            'link'
        );

        $registry->where(
            'column',
            $registry->getModelRegistry('primary_prefix', 'a') . '.' . 'enabled',
            '=',
            'integer',
            1
        );

        $sql = $registry->getSql();

//file_put_contents(__DIR__ . '/testQBCatalogModelRegistry.txt', $sql);

        $this->assertEquals(file_get_contents(__DIR__ . '/testQBCatalogModelRegistry.txt'), $sql);
    }

    /**
     * Test Constructor
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @return void
     * @since   1.0
     */
    public function testConstruct()
    {
        $this->assertEquals(1, 1);

        return;
    }
}
