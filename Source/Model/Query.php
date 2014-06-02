<?php
/**
 * Query Proxy
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Model;

use CommonApi\Query\QueryInterface;

/**
 * Query Proxy
 *
 * Base - Query - Utilities - Defaults - Columns - Criteria - Registry
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Query extends Base implements QueryInterface
{
    use \Molajo\Query\QueryTrait;

    /**
     * Class Constructor
     *
     * @param  QueryInterface $query
     *
     * @since  1.0.0
     */
    public function __construct(
        QueryInterface $query
    ) {
        $this->query = $query;
        $this->setDateProperties();
    }

    /**
     * Set Default Values for SQL
     *
     * @return  $this
     * @since   1.0
     */
    protected function setDateProperties()
    {
        $this->null_date    = $this->getNullDate();
        $this->current_date = $this->getDate();

        return $this;
    }
}
