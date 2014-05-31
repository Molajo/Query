<?php
/**
 * Query Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Query\ModelRegistryInterface;
use CommonApi\Query\QueryInterface;

/**
 * Read Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class QueryController extends ReadController implements QueryInterface, ModelRegistryInterface
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
     * Class Constructor
     *
     * @param  QueryInterface $qb
     * @param  array          $model_registry
     *
     * @since  1.0
     */
    public function __construct(
        QueryInterface $qb,
        ModelRegistryInterface $mr
    ) {
        $this->qb   = $qb;
        $this->mr   = $mr;
    }
}
