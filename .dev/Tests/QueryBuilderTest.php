<?php
/**
 * Query Builder Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use ReflectionClass;
use Molajo\Fieldhandler\MockRequest as Fieldhandler;
use Molajo\Database\MockDatabase as Database;
use Molajo\Query\Adapter\Mysql as QueryClass;
use Molajo\Query\QueryProxy;
use Molajo\Model\ReadModel;
use Molajo\Query\Model\Registry;
use Molajo\Query\QueryBuilder;

use PHPUnit_Framework_TestCase;

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
     * Test Get Method
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
     * @return void
     * @since   1.0
     */
    public function setup()
    {
/**
        $class   = new ReflectionClass('Molajo\Query\Adapter\Mysql');
        $methods = $class->getMethods();
        foreach ($methods as $method) {
            echo '     * @covers  ' . $method->class . '::' . $method->name . PHP_EOL;
        }
*/


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
