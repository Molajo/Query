<?php
/**
 * Query Builder Base
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

/**
 * Query Builder Base
 *
 * Sql - BuildSql - BuildSqlGroups - BuildSqlElements - SetData - EditData - FilterData - Base
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Base
{
    /**
     * Database
     *
     * @var    object  CommonApi\Database\DatabaseInterface
     * @since  1.0.0
     */
    protected $database = '';

    /**
     * Fieldhandler Instance
     *
     * @var    object  CommonApi\Query\FieldhandlerInterface
     * @since  1.0.0
     */
    protected $fieldhandler = '';

    /**
     * Database Prefix
     *
     * @var    string
     * @since  1.0.0
     */
    protected $database_prefix = '';

    /**
     * Query Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $query_type = 'select';

    /**
     * Distinct
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $distinct = false;

    /**
     * Columns
     *
     * @var    array
     * @since  1.0.0
     */
    protected $columns = array();

    /**
     * Values
     *
     * @var    array
     * @since  1.0.0
     */
    protected $values = array();

    /**
     * From
     *
     * @var    array
     * @since  1.0.0
     */
    protected $from = array();

    /**
     * Where Group
     *
     * @var    array
     * @since  1.0.0
     */
    protected $where_group = array();

    /**
     * Where
     *
     * @var    array
     * @since  1.0.0
     */
    protected $where = array();

    /**
     * Group By
     *
     * @var    array
     * @since  1.0.0
     */
    protected $group_by = array();

    /**
     * Having Group
     *
     * @var    array
     * @since  1.0.0
     */
    protected $having_group = array();

    /**
     * Having
     *
     * @var    array
     * @since  1.0.0
     */
    protected $having = array();

    /**
     * Order By
     *
     * @var    array
     * @since  1.0.0
     */
    protected $order_by = array();

    /**
     * Query Object
     *
     * List, Item, Result, Distinct
     *
     * @var    string
     * @since  1.0.0
     */
    protected $query_object;

    /**
     * Use Pagination
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $use_pagination;

    /**
     * Offset
     *
     * @var    int
     * @since  1.0.0
     */
    protected $offset = 0;

    /**
     * Limit
     *
     * @var    int
     * @since  1.0.0
     */
    protected $limit = 0;

    /**
     * SQL
     *
     * @var    string
     * @since  1.0.0
     */
    protected $sql = '';

    /**
     * Date Format - overridden by adapter, if needed
     *
     * @var    string
     * @since  1.0.0
     */
    protected $date_format = '';

    /**
     * Null Date - overridden by adapter, if needed
     *
     * @var    string
     * @since  1.0.0
     */
    protected $null_date = '';

    /**
     * Name quote start - overridden by adapter, if needed
     *
     * @var    string
     * @since  1.0.0
     */
    protected $name_quote_start = '"';

    /**
     * Name quote start - overridden by adapter, if needed
     *
     * @var    string
     * @since  1.0.0
     */
    protected $name_quote_end = '"';

    /**
     * Quote Value - overridden by adapter, if needed
     *
     * @var    string
     * @since  1.0.0
     */
    protected $quote_value = '"';

    /**
     * Connectors
     *
     * @var    array
     * @since  1.0.0
     */
    protected $connector = array('OR', 'AND');

    /**
     * QueryType
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_type_array = array('insert', 'insertfrom', 'select', 'update', 'delete', 'exec');

    /**
     * List of Controller Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'database_prefix',
            'query_type',
            'distinct',
            'columns',
            'values',
            'from',
            'where_group',
            'where',
            'group_by',
            'having_group',
            'having',
            'order_by',
            'offset',
            'limit',
            'date_format',
            'null_date'
        );
}
