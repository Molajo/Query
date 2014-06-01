<?php
/**
 * Query Builder
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use CommonApi\Query\QueryBuilderInterface;

/**
 * Query Builder and Model Registry
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * Query Builder Trait
     *
     * @var     object  Molajo\Query\QueryBuilderTrait
     * @since   1.0.0
     */
    use \Molajo\Query\QueryBuilderTrait;

    /**
     * Model Registry Trait
     *
     * @var     object  Molajo\Query\ModelRegistryTrait
     * @since   1.0.0
     */
    use \Molajo\Query\ModelRegistryTrait;

    /**
     * Query Adapter
     *
     * @var     object  CommonApi\Query\QueryBuilderInterface
     * @since   1.0
     */
    protected $qb;

    /**
     * Constructor
     *
     * @param  QueryBuilder  $qb
     *
     * @since  1.0
     */
    public function __construct(
        QueryBuilder $qb
    ) {
        $this->qb  = $qb;
    }
}
