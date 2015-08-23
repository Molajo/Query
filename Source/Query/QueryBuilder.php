<?php
/**
 * Query Builder
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use CommonApi\Query\QueryBuilderInterface;

/**
 * Query Builder
 *
 * Injected with Molajo\Query\Model\Registry (which extends Molajo\Query\Model\Query,
 *  a proxy to Molajo\Query\Builder\Driver)
 *
 * Injected into the Molajo\Controller\Query class
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * Query Builder Trait
     *
     * @var     object  Molajo\Query\QueryTrait
     * @since   1.0.0
     */
    use \Molajo\Query\QueryTrait;

    /**
     * Model Registry Trait
     *
     * @var     object  Molajo\Query\ModelRegistryTrait
     * @since   1.0.0
     */
    use \Molajo\Query\ModelRegistryTrait;

    /**
     * Constructor
     *
     * @param  QueryBuilderInterface $query
     *
     * @since  1.0
     */
    public function __construct(
        QueryBuilderInterface $query
    ) {
        $this->query = $query;
    }
}
