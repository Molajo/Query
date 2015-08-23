<?php
/**
 * Combined Query Builder and Database Driver
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use CommonApi\Query\DatabaseInterface;
use CommonApi\Query\QueryBuilderInterface;

/**
 * Combined Query Builder and Database Driver
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Database implements QueryBuilderInterface, DatabaseInterface
{
    /**
     * Data Trait
     *
     * @var     object  Molajo\Query\DataTrait
     * @since   1.0.0
     */
    use \Molajo\Query\DataTrait;

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
     * @param  DatabaseInterface     $database
     *
     * @since  1.0.0
     */
    public function __construct(
        QueryBuilderInterface $query,
        DatabaseInterface $database
    ) {
        $this->query    = $query;
        $this->database = $database;
    }
}
