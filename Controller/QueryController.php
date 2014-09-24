<?php
/**
 * Query Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Query\QueryBuilderInterface;
use CommonApi\Model\ModelInterface;

/**
 * Query Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class QueryController extends Base implements QueryBuilderInterface
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
     * Class Constructor
     *
     * @param  ModelInterface        $model
     * @param  array                 $runtime_data
     * @param  callable              $schedule_event
     * @param  QueryBuilderInterface $query
     *
     * @since  1.0
     */
    public function __construct(
        ModelInterface $model = null,
        $runtime_data = array(),
        callable $schedule_event = null,
        QueryBuilderInterface $query = null
    ) {
        parent::__construct(
            $model,
            $runtime_data,
            $schedule_event
        );

        $this->query = $query;

        $this->query->set('application_id', $this->application_id);
        $this->query->set('site_id', $this->site_id);
    }
}
