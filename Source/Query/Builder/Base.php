<?php
/**
 * Query Builder Base
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Builder;

/**
 * Query Builder Base
 *
 * Sql - BuildSql - BuildSqlElements - BuildSqlGroups - SetData - EditData - FilterData - Base
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Base
{
    /**
     * Select Array Groups
     *
     * @var    array
     * @since  1.0.0
     */
    protected $select_array
        = array(
            'columns',
            'from',
            'where',
            'order_by',
            'having',
            'group_by',
            'limit'
        );

    /**
     * Insert Array Groups
     *
     * @var    array
     * @since  1.0.0
     */
    protected $insert_array
        = array(
            'columns',
            'values'
        );

    /**
     * Groups array for processing
     *
     * @var    array
     * @since  1.0.0
     */
    protected $groups_array
        = array(
            'columns'        => array(
                'type'            => 'columns',
                'get_value'       => false,
                'get_column'      => true,
                'use_alias'       => true,
                'group_connector' => '',
                'return_literal'  => '',
                'key_value'       => 0,
                'format'          => 1
            ),
            'update_columns' => array(
                'type'            => 'columns',
                'get_value'       => true,
                'get_column'      => true,
                'use_alias'       => false,
                'group_connector' => '',
                'return_literal'  => '',
                'key_value'       => 1,
                'format'          => 1
            ),
            'values'         => array(
                'type'            => 'values',
                'get_value'       => true,
                'get_column'      => false,
                'use_alias'       => false,
                'group_connector' => '',
                'return_literal'  => 'VALUES (',
                'key_value'       => 0,
                'format'          => 1
            ),
            'from'           => array(
                'type'            => 'from',
                'get_value'       => false,
                'get_column'      => true,
                'use_alias'       => true,
                'group_connector' => '',
                'return_literal'  => 'FROM',
                'key_value'       => 0,
                'format'          => 1
            ),
            'where'          => array(
                'type'           => 'where',
                'get_value'      => true,
                'get_column'     => true,
                'use_alias'      => false,
                'connector'      => 'AND',
                'return_literal' => 'WHERE',
                'key_value'      => 1,
                'format'         => 1
            ),
            'order_by'       => array(
                'type'           => 'order_by',
                'get_value'      => false,
                'get_column'     => true,
                'use_alias'      => false,
                'connector'      => '',
                'return_literal' => 'ORDER BY',
                'key_value'      => 0,
                'format'         => 1
            ),
            'having'         => array(
                'type'           => 'having',
                'get_value'      => false,
                'get_column'     => true,
                'use_alias'      => false,
                'connector'      => 'AND',
                'return_literal' => 'HAVING',
                'key_value'      => 1,
                'format'         => 1
            ),
            'group_by'       => array(
                'type'           => 'group_by',
                'get_value'      => false,
                'get_column'     => true,
                'use_alias'      => false,
                'connector'      => '',
                'return_literal' => 'GROUP BY',
                'key_value'      => 0,
                'format'         => 1
            ),
            'limit'          => array(
                'type' => 'limit'
            )
        );

    /**
     * Database
     *
     * @var    object  CommonApi\Query\DatabaseInterface
     * @since  1.0.0
     */
    protected $database = '';

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
     * Update Columns
     *
     * @var    array
     * @since  1.0.0
     */
    protected $update_columns = array();

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
     * Insert into table
     *
     * @var    string
     * @since  1.0.0
     */
    protected $insert_into_table = null;

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
    protected $query_object = 'list';

    /**
     * Use Pagination
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $use_pagination = 0;

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
