<?php
/**
 * ModelRegistryQuery Test
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Controller\ControllerInterface;
use CommonApi\Database\DatabaseInterface;
use CommonApi\Model\ModelInterface;
use CommonApi\Query\QueryInterface;
use Molajo\Database\MockDatabase;
use Molajo\Model\ReadModel;
use Molajo\Query\Adapter\MySQL;
use Molajo\Fieldhandler\Request;
use Molajo\Query\Driver as Query;

use PHPUnit_Framework_TestCase;

/**
 * ModelRegistryQuery
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ModelRegistryQueryTest extends PHPUnit_Framework_TestCase
{
    protected $controller;

    /**
     * Test Empty Model Registry - defaults
     *
     * @covers  Molajo\Controller\ModelRegistryQuery::__construct
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelRegistrySQL
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumns
     * @covers  Molajo\Controller\ModelRegistryQuery::useSelectColumns
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumnsResultQuery
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumnsDistinctQuery
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumnsModelRegistry
     * @covers  Molajo\Controller\ModelRegistryQuery::setFromTable
     * @covers  Molajo\Controller\ModelRegistryQuery::setKeyCriteria
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereStatementsKeyValue
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoins
     * @covers  Molajo\Controller\ModelRegistryQuery::useJoins
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoinItem
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoinItemColumns
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoinItemWhere
     * @covers  Molajo\Controller\ModelRegistryQuery::useJoinItemWhere
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelCriteria
     * @covers  Molajo\Controller\ModelRegistryQuery::useModelCriteriaWhere
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelCriteriaArrayCriteria
     * @covers  Molajo\Controller\ModelRegistryQuery::useModelCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelRegistryCriteriaArrayItem
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelRegistryLimits
     * @covers  Molajo\Controller\ModelRegistryQuery::setWherePair
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereOperator
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElement
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElementProperty
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElementNumericValue
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElementTableColumn
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelect
     * @covers  Molajo\Controller\ModelRegistryQuery::setFrom
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhere
     *
     * @covers  Molajo\Controller\ModelRegistryDefaults::__construct
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaults
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryBase
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistryDefaults::getModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::verifyPropertyExists
     * @covers  Molajo\Controller\ModelRegistryDefaults::setProperty
     *
     * @covers  Molajo\Controller\QueryController::__construct
     * @covers  Molajo\Controller\QueryController::setDateProperties
     * @covers  Molajo\Controller\QueryController::get
     * @covers  Molajo\Controller\QueryController::clearQuery
     * @covers  Molajo\Controller\QueryController::setType
     * @covers  Molajo\Controller\QueryController::getDateFormat
     * @covers  Molajo\Controller\QueryController::getDate
     * @covers  Molajo\Controller\QueryController::getNullDate
     * @covers  Molajo\Controller\QueryController::setDistinct
     * @covers  Molajo\Controller\QueryController::select
     * @covers  Molajo\Controller\QueryController::from
     * @covers  Molajo\Controller\QueryController::whereGroup
     * @covers  Molajo\Controller\QueryController::where
     * @covers  Molajo\Controller\QueryController::groupBy
     * @covers  Molajo\Controller\QueryController::havingGroup
     * @covers  Molajo\Controller\QueryController::having
     * @covers  Molajo\Controller\QueryController::orderBy
     * @covers  Molajo\Controller\QueryController::setOffsetAndLimit
     * @covers  Molajo\Controller\QueryController::getSQL
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @covers  Molajo\Query\Driver::__construct
     * @covers  Molajo\Query\Driver::get
     * @covers  Molajo\Query\Driver::clearQuery
     * @covers  Molajo\Query\Driver::setType
     * @covers  Molajo\Query\Driver::getDateFormat
     * @covers  Molajo\Query\Driver::getDate
     * @covers  Molajo\Query\Driver::getNullDate
     * @covers  Molajo\Query\Driver::setDistinct
     * @covers  Molajo\Query\Driver::select
     * @covers  Molajo\Query\Driver::from
     * @covers  Molajo\Query\Driver::whereGroup
     * @covers  Molajo\Query\Driver::where
     * @covers  Molajo\Query\Driver::groupBy
     * @covers  Molajo\Query\Driver::havingGroup
     * @covers  Molajo\Query\Driver::having
     * @covers  Molajo\Query\Driver::orderBy
     * @covers  Molajo\Query\Driver::setOffsetAndLimit
     * @covers  Molajo\Query\Driver::getSQL
     *
     * @covers  Molajo\Query\Adapter\AbstractAdapter::__construct
     * @covers  Molajo\Query\Adapter\AbstractAdapter::get
     * @covers  Molajo\Query\Adapter\AbstractAdapter::clearQuery
     * @covers  Molajo\Query\Adapter\AbstractAdapter::getDateFormat
     * @covers  Molajo\Query\Adapter\AbstractAdapter::getDate
     * @covers  Molajo\Query\Adapter\AbstractAdapter::getNullDate
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editArray
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editDataType
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editRequired
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editConnector
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editWhere
     * @covers  Molajo\Query\Adapter\AbstractAdapter::setOrFilterColumn
     * @covers  Molajo\Query\Adapter\AbstractAdapter::quoteValue
     * @covers  Molajo\Query\Adapter\AbstractAdapter::quoteNameAndAlias
     * @covers  Molajo\Query\Adapter\AbstractAdapter::quoteName
     * @covers  Molajo\Query\Adapter\AbstractAdapter::filter
     *
     * @covers  Molajo\Query\Adapter\AbstractCollect::__construct
     * @covers  Molajo\Query\Adapter\AbstractCollect::setType
     * @covers  Molajo\Query\Adapter\AbstractCollect::setDistinct
     * @covers  Molajo\Query\Adapter\AbstractCollect::select
     * @covers  Molajo\Query\Adapter\AbstractCollect::selectColumn
     * @covers  Molajo\Query\Adapter\AbstractCollect::selectDataType
     * @covers  Molajo\Query\Adapter\AbstractCollect::from
     * @covers  Molajo\Query\Adapter\AbstractCollect::whereGroup
     * @covers  Molajo\Query\Adapter\AbstractCollect::where
     * @covers  Molajo\Query\Adapter\AbstractCollect::groupBy
     * @covers  Molajo\Query\Adapter\AbstractCollect::havingGroup
     * @covers  Molajo\Query\Adapter\AbstractCollect::having
     * @covers  Molajo\Query\Adapter\AbstractCollect::buildItem
     * @covers  Molajo\Query\Adapter\AbstractCollect::orderBy
     * @covers  Molajo\Query\Adapter\AbstractCollect::setOffsetAndLimit
     * @covers  Molajo\Query\Adapter\AbstractCollect::setColumnName
     * @covers  Molajo\Query\Adapter\AbstractCollect::processInArray
     *
     * @covers  Molajo\Query\Adapter\AbstractConstruct::__construct
     * @covers  Molajo\Query\Adapter\AbstractConstruct::getSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::getSQLExternal
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsert
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsertColumns
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsertValues
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsertfrom
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelect
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectDistinct
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectColumns
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectColumnsConnector
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectColumnsAlias
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectFrom
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectFrom
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectGroupBy
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectHaving
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectOrderBy
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectOrderGroupHaving
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectLimit
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLUpdate
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLUpdateColumns
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLDelete
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setColumnName
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setWhere
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setFromSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setWhereSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLWhereGroup
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setGroupBySQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setHavingSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLHavingGroup
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLGroupBeginning
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setOrderBySQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLLimit
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setDatabasePrefix
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setLoopSQL
     *
     * @covers  Molajo\Query\Adapter\Mysql::__construct
     * @covers  Molajo\Query\Adapter\Postgresql::__construct
     * @covers  Molajo\Query\Adapter\Sqllite::__construct
     * @covers  Molajo\Query\Adapter\Sqlserver::__construct
     *
     * @return  void
     * @since   1.0
     */
    public function testDefaultModelRegistry()
    {
        $fieldhandler    = new Request();
        $database_prefix = 'molajo_';
        $database        = new MockDatabase();

        $mysql = new MySQL(
            $fieldhandler,
            $database_prefix,
            $database
        );

        $query = new Query($mysql);

        $model          = new ReadModel($database);
        $runtime_data   = array();
        $plugin_data    = array();
        $schedule_event = 'strtolower';

        $this->controller = new ModelRegistryQuery(
            $query,
            $model,
            $runtime_data,
            $plugin_data,
            $schedule_event
        );

        $model_registry = $this->controller->getModelRegistry();

        $this->assertEquals('list', $model_registry['query_object']);
        $this->assertEquals(1, $model_registry['process_events']);
        $this->assertEquals('', $model_registry['criteria_status']);
        $this->assertEquals(0, $model_registry['criteria_source_id']);
        $this->assertEquals(0, $model_registry['criteria_catalog_type_id']);
        $this->assertEquals(0, $model_registry['catalog_type_id']);
        $this->assertEquals(0, $model_registry['menu_id']);
        $this->assertEquals(0, $model_registry['criteria_extension_instance_id']);
        $this->assertEquals('id', $model_registry['primary_key']);
        $this->assertEquals('', $model_registry['primary_key_value']);
        $this->assertEquals('title', $model_registry['name_key']);
        $this->assertEquals('', $model_registry['name_key_value']);
        $this->assertTrue(is_array($model_registry['fields']));
        $this->assertEquals('#__content', $model_registry['table_name']);
        $this->assertEquals('a', $model_registry['primary_prefix']);
        $this->assertTrue(is_array($model_registry['criteria']));
        $this->assertEquals(0, $model_registry['use_special_joins']);
        $this->assertTrue(is_array($model_registry['joins']));
        $this->assertEquals(0, $model_registry['model_offset']);
        $this->assertEquals(15, $model_registry['model_count']);
        $this->assertEquals(1, $model_registry['use_pagination']);

        $sql = $this->controller->getSQL();

        $expected_sql
            = 'SELECT `a`.*
FROM `molajo_content` AS `a`
';
        $this->assertEquals($expected_sql, $sql);

        return;
    }

    /**
     * Test Catalog Model without any joins or changes
     *
     * @covers  Molajo\Controller\ModelRegistryQuery::__construct
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelRegistrySQL
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumns
     * @covers  Molajo\Controller\ModelRegistryQuery::useSelectColumns
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumnsResultQuery
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumnsDistinctQuery
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumnsModelRegistry
     * @covers  Molajo\Controller\ModelRegistryQuery::setFromTable
     * @covers  Molajo\Controller\ModelRegistryQuery::setKeyCriteria
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereStatementsKeyValue
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoins
     * @covers  Molajo\Controller\ModelRegistryQuery::useJoins
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoinItem
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoinItemColumns
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoinItemWhere
     * @covers  Molajo\Controller\ModelRegistryQuery::useJoinItemWhere
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelCriteria
     * @covers  Molajo\Controller\ModelRegistryQuery::useModelCriteriaWhere
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelCriteriaArrayCriteria
     * @covers  Molajo\Controller\ModelRegistryQuery::useModelCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelRegistryCriteriaArrayItem
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelRegistryLimits
     * @covers  Molajo\Controller\ModelRegistryQuery::setWherePair
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereOperator
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElement
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElementProperty
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElementNumericValue
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElementTableColumn
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelect
     * @covers  Molajo\Controller\ModelRegistryQuery::setFrom
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhere
     *
     * @covers  Molajo\Controller\ModelRegistryDefaults::__construct
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaults
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryBase
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistryDefaults::getModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::verifyPropertyExists
     * @covers  Molajo\Controller\ModelRegistryDefaults::setProperty
     *
     * @covers  Molajo\Controller\QueryController::__construct
     * @covers  Molajo\Controller\QueryController::setDateProperties
     * @covers  Molajo\Controller\QueryController::get
     * @covers  Molajo\Controller\QueryController::clearQuery
     * @covers  Molajo\Controller\QueryController::setType
     * @covers  Molajo\Controller\QueryController::getDateFormat
     * @covers  Molajo\Controller\QueryController::getDate
     * @covers  Molajo\Controller\QueryController::getNullDate
     * @covers  Molajo\Controller\QueryController::setDistinct
     * @covers  Molajo\Controller\QueryController::select
     * @covers  Molajo\Controller\QueryController::from
     * @covers  Molajo\Controller\QueryController::whereGroup
     * @covers  Molajo\Controller\QueryController::where
     * @covers  Molajo\Controller\QueryController::groupBy
     * @covers  Molajo\Controller\QueryController::havingGroup
     * @covers  Molajo\Controller\QueryController::having
     * @covers  Molajo\Controller\QueryController::orderBy
     * @covers  Molajo\Controller\QueryController::setOffsetAndLimit
     * @covers  Molajo\Controller\QueryController::getSQL
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @covers  Molajo\Query\Driver::__construct
     * @covers  Molajo\Query\Driver::get
     * @covers  Molajo\Query\Driver::clearQuery
     * @covers  Molajo\Query\Driver::setType
     * @covers  Molajo\Query\Driver::getDateFormat
     * @covers  Molajo\Query\Driver::getDate
     * @covers  Molajo\Query\Driver::getNullDate
     * @covers  Molajo\Query\Driver::setDistinct
     * @covers  Molajo\Query\Driver::select
     * @covers  Molajo\Query\Driver::from
     * @covers  Molajo\Query\Driver::whereGroup
     * @covers  Molajo\Query\Driver::where
     * @covers  Molajo\Query\Driver::groupBy
     * @covers  Molajo\Query\Driver::havingGroup
     * @covers  Molajo\Query\Driver::having
     * @covers  Molajo\Query\Driver::orderBy
     * @covers  Molajo\Query\Driver::setOffsetAndLimit
     * @covers  Molajo\Query\Driver::getSQL
     *
     * @covers  Molajo\Query\Adapter\AbstractAdapter::__construct
     * @covers  Molajo\Query\Adapter\AbstractAdapter::get
     * @covers  Molajo\Query\Adapter\AbstractAdapter::clearQuery
     * @covers  Molajo\Query\Adapter\AbstractAdapter::getDateFormat
     * @covers  Molajo\Query\Adapter\AbstractAdapter::getDate
     * @covers  Molajo\Query\Adapter\AbstractAdapter::getNullDate
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editArray
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editDataType
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editRequired
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editConnector
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editWhere
     * @covers  Molajo\Query\Adapter\AbstractAdapter::setOrFilterColumn
     * @covers  Molajo\Query\Adapter\AbstractAdapter::quoteValue
     * @covers  Molajo\Query\Adapter\AbstractAdapter::quoteNameAndAlias
     * @covers  Molajo\Query\Adapter\AbstractAdapter::quoteName
     * @covers  Molajo\Query\Adapter\AbstractAdapter::filter
     *
     * @covers  Molajo\Query\Adapter\AbstractCollect::__construct
     * @covers  Molajo\Query\Adapter\AbstractCollect::setType
     * @covers  Molajo\Query\Adapter\AbstractCollect::setDistinct
     * @covers  Molajo\Query\Adapter\AbstractCollect::select
     * @covers  Molajo\Query\Adapter\AbstractCollect::selectColumn
     * @covers  Molajo\Query\Adapter\AbstractCollect::selectDataType
     * @covers  Molajo\Query\Adapter\AbstractCollect::from
     * @covers  Molajo\Query\Adapter\AbstractCollect::whereGroup
     * @covers  Molajo\Query\Adapter\AbstractCollect::where
     * @covers  Molajo\Query\Adapter\AbstractCollect::groupBy
     * @covers  Molajo\Query\Adapter\AbstractCollect::havingGroup
     * @covers  Molajo\Query\Adapter\AbstractCollect::having
     * @covers  Molajo\Query\Adapter\AbstractCollect::buildItem
     * @covers  Molajo\Query\Adapter\AbstractCollect::orderBy
     * @covers  Molajo\Query\Adapter\AbstractCollect::setOffsetAndLimit
     * @covers  Molajo\Query\Adapter\AbstractCollect::setColumnName
     * @covers  Molajo\Query\Adapter\AbstractCollect::processInArray
     *
     * @covers  Molajo\Query\Adapter\AbstractConstruct::__construct
     * @covers  Molajo\Query\Adapter\AbstractConstruct::getSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::getSQLExternal
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsert
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsertColumns
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsertValues
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsertfrom
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelect
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectDistinct
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectColumns
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectColumnsConnector
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectColumnsAlias
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectFrom
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectFrom
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectGroupBy
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectHaving
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectOrderBy
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectOrderGroupHaving
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectLimit
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLUpdate
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLUpdateColumns
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLDelete
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setColumnName
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setWhere
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setFromSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setWhereSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLWhereGroup
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setGroupBySQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setHavingSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLHavingGroup
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLGroupBeginning
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setOrderBySQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLLimit
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setDatabasePrefix
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setLoopSQL
     *
     * @covers  Molajo\Query\Adapter\Mysql::__construct
     * @covers  Molajo\Query\Adapter\Postgresql::__construct
     * @covers  Molajo\Query\Adapter\Sqllite::__construct
     * @covers  Molajo\Query\Adapter\Sqlserver::__construct
     *
     * @return  void
     * @since   1.0
     */
    public function testCatalogModelRegistry()
    {
        $fieldhandler    = new Request();
        $database_prefix = 'molajo_';
        $database        = new MockDatabase();

        $mysql = new MySQL(
            $fieldhandler,
            $database_prefix,
            $database
        );

        $query = new Query($mysql);

        $model          = new ReadModel($database);
        $runtime_data   = array();
        $plugin_data    = array();
        $schedule_event = 'strtolower';

        $mock           = new MockModelRegistry();
        $model_registry = $mock->create();

        $this->controller = new ModelRegistryQuery(
            $query,
            $model,
            $runtime_data,
            $plugin_data,
            $schedule_event,
            $model_registry
        );

        $model_registry = $this->controller->getModelRegistry();

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
        $this->assertEquals('application_id', $model_registry['fields']['1']['name']);
        $this->assertEquals('integer', $model_registry['fields']['1']['type']);
        $this->assertEquals(0, $model_registry['fields']['1']['null']);
        $this->assertEquals('', $model_registry['fields']['1']['default']);
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
        $this->assertEquals('#__application_extension_instances', $model_registry['joins']['1']['table_name']);
        $this->assertEquals('application_extension_instances', $model_registry['joins']['1']['alias']);
        $this->assertEquals('', $model_registry['joins']['1']['select']);
        $this->assertEquals('application_id,extension_instance_id', $model_registry['joins']['1']['jointo']);
        $this->assertEquals('APPLICATION_ID,extension_instance_id', $model_registry['joins']['1']['joinwith']);
        $this->assertEquals('#__site_extension_instances', $model_registry['joins']['2']['table_name']);
        $this->assertEquals('site_extension_instances', $model_registry['joins']['2']['alias']);
        $this->assertEquals('', $model_registry['joins']['2']['select']);
        $this->assertEquals('site_id,extension_instance_id', $model_registry['joins']['2']['jointo']);
        $this->assertEquals('SITE_ID,extension_instance_id', $model_registry['joins']['2']['joinwith']);
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

        $sql = $this->controller->getSQL();
//file_put_contents(__DIR__ . '/expected.txt', $sql);

        $this->assertEquals(file_get_contents(__DIR__ . '/expected.txt'), $sql);

        return;
    }

    /**
     * Test Catalog Model For Route
     *
     * @covers  Molajo\Controller\ModelRegistryQuery::__construct
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelRegistrySQL
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumns
     * @covers  Molajo\Controller\ModelRegistryQuery::useSelectColumns
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumnsResultQuery
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumnsDistinctQuery
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelectColumnsModelRegistry
     * @covers  Molajo\Controller\ModelRegistryQuery::setFromTable
     * @covers  Molajo\Controller\ModelRegistryQuery::setKeyCriteria
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereStatementsKeyValue
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoins
     * @covers  Molajo\Controller\ModelRegistryQuery::useJoins
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoinItem
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoinItemColumns
     * @covers  Molajo\Controller\ModelRegistryQuery::setJoinItemWhere
     * @covers  Molajo\Controller\ModelRegistryQuery::useJoinItemWhere
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelCriteria
     * @covers  Molajo\Controller\ModelRegistryQuery::useModelCriteriaWhere
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelCriteriaArrayCriteria
     * @covers  Molajo\Controller\ModelRegistryQuery::useModelCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelRegistryCriteriaArrayItem
     * @covers  Molajo\Controller\ModelRegistryQuery::setModelRegistryLimits
     * @covers  Molajo\Controller\ModelRegistryQuery::setWherePair
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereOperator
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElement
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElementProperty
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElementNumericValue
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhereElementTableColumn
     * @covers  Molajo\Controller\ModelRegistryQuery::setSelect
     * @covers  Molajo\Controller\ModelRegistryQuery::setFrom
     * @covers  Molajo\Controller\ModelRegistryQuery::setWhere
     *
     * @covers  Molajo\Controller\ModelRegistryDefaults::__construct
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaults
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryBase
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsGroup
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryCriteriaValues
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsKeys
     * @covers  Molajo\Controller\ModelRegistryDefaults::getModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPrimaryKeyValue
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsFields
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsTableName
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsPrimaryPrefix
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsQueryObject
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsCriteriaArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultsJoins
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryDefaultLimits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setModelRegistryPaginationCrossEdits
     * @covers  Molajo\Controller\ModelRegistryDefaults::setPropertyArray
     * @covers  Molajo\Controller\ModelRegistryDefaults::verifyPropertyExists
     * @covers  Molajo\Controller\ModelRegistryDefaults::setProperty
     *
     * @covers  Molajo\Controller\QueryController::__construct
     * @covers  Molajo\Controller\QueryController::setDateProperties
     * @covers  Molajo\Controller\QueryController::get
     * @covers  Molajo\Controller\QueryController::clearQuery
     * @covers  Molajo\Controller\QueryController::setType
     * @covers  Molajo\Controller\QueryController::getDateFormat
     * @covers  Molajo\Controller\QueryController::getDate
     * @covers  Molajo\Controller\QueryController::getNullDate
     * @covers  Molajo\Controller\QueryController::setDistinct
     * @covers  Molajo\Controller\QueryController::select
     * @covers  Molajo\Controller\QueryController::from
     * @covers  Molajo\Controller\QueryController::whereGroup
     * @covers  Molajo\Controller\QueryController::where
     * @covers  Molajo\Controller\QueryController::groupBy
     * @covers  Molajo\Controller\QueryController::havingGroup
     * @covers  Molajo\Controller\QueryController::having
     * @covers  Molajo\Controller\QueryController::orderBy
     * @covers  Molajo\Controller\QueryController::setOffsetAndLimit
     * @covers  Molajo\Controller\QueryController::getSQL
     *
     * @covers  Molajo\Controller\Controller::__construct
     * @covers  Molajo\Controller\Controller::getValue
     * @covers  Molajo\Controller\Controller::setValue
     * @covers  Molajo\Controller\Controller::setSiteApplicationProperties
     *
     * @covers  Molajo\Query\Driver::__construct
     * @covers  Molajo\Query\Driver::get
     * @covers  Molajo\Query\Driver::clearQuery
     * @covers  Molajo\Query\Driver::setType
     * @covers  Molajo\Query\Driver::getDateFormat
     * @covers  Molajo\Query\Driver::getDate
     * @covers  Molajo\Query\Driver::getNullDate
     * @covers  Molajo\Query\Driver::setDistinct
     * @covers  Molajo\Query\Driver::select
     * @covers  Molajo\Query\Driver::from
     * @covers  Molajo\Query\Driver::whereGroup
     * @covers  Molajo\Query\Driver::where
     * @covers  Molajo\Query\Driver::groupBy
     * @covers  Molajo\Query\Driver::havingGroup
     * @covers  Molajo\Query\Driver::having
     * @covers  Molajo\Query\Driver::orderBy
     * @covers  Molajo\Query\Driver::setOffsetAndLimit
     * @covers  Molajo\Query\Driver::getSQL
     *
     * @covers  Molajo\Query\Adapter\AbstractAdapter::__construct
     * @covers  Molajo\Query\Adapter\AbstractAdapter::get
     * @covers  Molajo\Query\Adapter\AbstractAdapter::clearQuery
     * @covers  Molajo\Query\Adapter\AbstractAdapter::getDateFormat
     * @covers  Molajo\Query\Adapter\AbstractAdapter::getDate
     * @covers  Molajo\Query\Adapter\AbstractAdapter::getNullDate
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editArray
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editDataType
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editRequired
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editConnector
     * @covers  Molajo\Query\Adapter\AbstractAdapter::editWhere
     * @covers  Molajo\Query\Adapter\AbstractAdapter::setOrFilterColumn
     * @covers  Molajo\Query\Adapter\AbstractAdapter::quoteValue
     * @covers  Molajo\Query\Adapter\AbstractAdapter::quoteNameAndAlias
     * @covers  Molajo\Query\Adapter\AbstractAdapter::quoteName
     * @covers  Molajo\Query\Adapter\AbstractAdapter::filter
     *
     * @covers  Molajo\Query\Adapter\AbstractCollect::__construct
     * @covers  Molajo\Query\Adapter\AbstractCollect::setType
     * @covers  Molajo\Query\Adapter\AbstractCollect::setDistinct
     * @covers  Molajo\Query\Adapter\AbstractCollect::select
     * @covers  Molajo\Query\Adapter\AbstractCollect::selectColumn
     * @covers  Molajo\Query\Adapter\AbstractCollect::selectDataType
     * @covers  Molajo\Query\Adapter\AbstractCollect::from
     * @covers  Molajo\Query\Adapter\AbstractCollect::whereGroup
     * @covers  Molajo\Query\Adapter\AbstractCollect::where
     * @covers  Molajo\Query\Adapter\AbstractCollect::groupBy
     * @covers  Molajo\Query\Adapter\AbstractCollect::havingGroup
     * @covers  Molajo\Query\Adapter\AbstractCollect::having
     * @covers  Molajo\Query\Adapter\AbstractCollect::buildItem
     * @covers  Molajo\Query\Adapter\AbstractCollect::orderBy
     * @covers  Molajo\Query\Adapter\AbstractCollect::setOffsetAndLimit
     * @covers  Molajo\Query\Adapter\AbstractCollect::setColumnName
     * @covers  Molajo\Query\Adapter\AbstractCollect::processInArray
     *
     * @covers  Molajo\Query\Adapter\AbstractConstruct::__construct
     * @covers  Molajo\Query\Adapter\AbstractConstruct::getSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::getSQLExternal
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsert
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsertColumns
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsertValues
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLInsertfrom
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelect
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectDistinct
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectColumns
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectColumnsConnector
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectColumnsAlias
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectFrom
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectFrom
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectGroupBy
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectHaving
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectOrderBy
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectOrderGroupHaving
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLSelectLimit
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLUpdate
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLUpdateColumns
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLDelete
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setColumnName
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setWhere
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setFromSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setWhereSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLWhereGroup
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setGroupBySQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setHavingSQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLHavingGroup
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLGroupBeginning
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setOrderBySQL
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setSQLLimit
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setDatabasePrefix
     * @covers  Molajo\Query\Adapter\AbstractConstruct::setLoopSQL
     *
     * @covers  Molajo\Query\Adapter\Mysql::__construct
     * @covers  Molajo\Query\Adapter\Postgresql::__construct
     * @covers  Molajo\Query\Adapter\Sqllite::__construct
     * @covers  Molajo\Query\Adapter\Sqlserver::__construct
     *
     * @return  void
     * @since   1.0
     */
    public function testCatalogModelRouteQuery()
    {
        $fieldhandler    = new Request();
        $database_prefix = 'molajo_';
        $database        = new MockDatabase();

        $mysql = new MySQL(
            $fieldhandler,
            $database_prefix,
            $database
        );

        $query = new Query($mysql);

        $model          = new ReadModel($database);
        $runtime_data   = array();
        $plugin_data    = array();
        $schedule_event = 'strtolower';

        $mock           = new MockModelRegistry();
        $model_registry = $mock->create();

        $this->controller = new ModelRegistryQuery(
            $query,
            $model,
            $runtime_data,
            $plugin_data,
            $schedule_event,
            $model_registry
        );

        $model_registry = $this->controller->getModelRegistry();

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
        $this->assertEquals('application_id', $model_registry['fields']['1']['name']);
        $this->assertEquals('integer', $model_registry['fields']['1']['type']);
        $this->assertEquals(0, $model_registry['fields']['1']['null']);
        $this->assertEquals('', $model_registry['fields']['1']['default']);
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
        $this->assertEquals('#__application_extension_instances', $model_registry['joins']['1']['table_name']);
        $this->assertEquals('application_extension_instances', $model_registry['joins']['1']['alias']);
        $this->assertEquals('', $model_registry['joins']['1']['select']);
        $this->assertEquals('application_id,extension_instance_id', $model_registry['joins']['1']['jointo']);
        $this->assertEquals('APPLICATION_ID,extension_instance_id', $model_registry['joins']['1']['joinwith']);
        $this->assertEquals('#__site_extension_instances', $model_registry['joins']['2']['table_name']);
        $this->assertEquals('site_extension_instances', $model_registry['joins']['2']['alias']);
        $this->assertEquals('', $model_registry['joins']['2']['select']);
        $this->assertEquals('site_id,extension_instance_id', $model_registry['joins']['2']['jointo']);
        $this->assertEquals('SITE_ID,extension_instance_id', $model_registry['joins']['2']['joinwith']);
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
        $this->controller->setModelRegistry('use_special_joins', 1);
        $this->controller->setModelRegistry('process_events', 0);
        $this->controller->setModelRegistry('query_object', 'item');

        $route_path = 'articles';

        $this->controller->where(
            'column',
            $this->controller->getModelRegistry('primary_prefix', 'a') . '.' . 'sef_request',
            '=',
            'string',
            $route_path
        );

        $this->controller->where(
            'column',
            $this->controller->getModelRegistry('primary_prefix', 'a') . '.' . 'page_type',
            '<>',
            'string',
            'link'
        );

        $this->controller->where(
            'column',
            $this->controller->getModelRegistry('primary_prefix', 'a') . '.' . 'enabled',
            '=',
            'integer',
            1
        );

        $this->controller->where(
            'column',
            $this->controller->getModelRegistry('primary_prefix', 'a') . '.' . 'application_id',
            '=',
            'integer',
            2
//todo: fix application issue
        );

        $sql = $this->controller->getSQL();
//file_put_contents(__DIR__ . '/expectedRoute.txt', $sql);

        $this->assertEquals(file_get_contents(__DIR__ . '/expectedRoute.txt'), $sql);

        return;
    }
}
