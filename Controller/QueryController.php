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
use CommonApi\Model\ModelInterface;

/**
 * Query Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class QueryController extends Controller implements QueryInterface, ModelRegistryInterface
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
     * @param  ModelInterface         $model
     * @param  array                  $runtime_data
     * @param  array                  $plugin_data
     * @param  callable               $schedule_event
     * @param  ModelRegistryInterface $model
     *
     * @since  1.0
     */
    public function __construct(
        ModelInterface $model = null,
        $runtime_data = array(),
        $plugin_data = array(),
        callable $schedule_event = null,
        ModelRegistryInterface $mr = null
    ) {
        parent::__construct(
            $model,
            $runtime_data,
            $plugin_data,
            $schedule_event
        );

        $this->mr = $mr;
    }
}
