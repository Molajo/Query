<?php
/**
 * Query Interface
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use CommonApi\Query\QueryInterface;

/**
 * Query Proxy
 *
 * Injected into Molajo\Query\Model\Registry for use as child object Molajo\Query\Model\Query
 *
 * The Registry object is then injected into the Molajo\Controller\Query class using
 *  Molajo\Query\QueryBuilder
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class QueryProxy implements QueryInterface
{
    /**
     * Query Proxy Trait
     *
     * @var     object  Molajo\Query\QueryTrait
     * @since   1.0.0
     */
    use \Molajo\Query\QueryTrait;

    /**
     * Constructor
     *
     * @param  QueryProxy $query
     *
     * @since  1.0
     */
    public function __construct(
        QueryInterface $query
    ) {
        $this->query = $query;
    }
}
