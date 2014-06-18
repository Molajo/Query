<?php
/**
 * Model Registry Base
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

/**
 * Model Registry Base
 *
 * Base - Query - Utilities - Defaults - Table - Columns - Criteria - Registry
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Base
{
    /**
     * Site ID
     *
     * @var    int
     * @since  1.0
     */
    protected $site_id = null;

    /**
     * Application ID
     *
     * @var    int
     * @since  1.0
     */
    protected $application_id = null;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $model_registry = array();

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $query_where_property_array
        = array(
            'APPLICATION_ID'  => 'application_id',
            'SITE_ID'         => 'site_id',
            'MENU_ID'         => 'criteria_menu_id',
            'CATALOG_TYPE_ID' => 'catalog_type_id',
        );

    /**
     * Operator Array
     *
     * @var    array
     * @since  1.0
     */
    protected $operator_array = array('=', '>=', '>', '<=', '<', '<>');


    /**
     * List of Valid Values for Query Object
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_query_object_values
        = array(
            'result',
            'item',
            'list',
            'distinct'
        );
}
