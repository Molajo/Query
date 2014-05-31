<?php
/**
 * Query Builder Proxy
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Controller;

use CommonApi\Query\ModelRegistryInterface;

/**
 * Query Builder Proxy
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
abstract class QueryBuilder extends Controller implements ModelRegistryInterface
{
    /**
     * Query Object
     *
     * List, Item, Result, Distinct
     *
     * @var    string
     * @since  1.0
     */
    protected $query_object;

    /**
     * Use Pagination
     *
     * @var    integer
     * @since  1.0
     */
    protected $use_pagination;

    /**
     * Offset
     *
     * @var    integer
     * @since  1.0
     */
    protected $offset;

    /**
     * Count
     *
     * @var    integer
     * @since  1.0
     */
    protected $count;

    /**
     * Offset Count
     *
     * @var    integer
     * @since  1.0
     */
    protected $offset_count;

    /**
     * Total
     *
     * @var    integer
     * @since  1.0
     */
    protected $total;

}
