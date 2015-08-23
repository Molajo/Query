<?php
/**
 * Event Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Event\TriggerInterface;
use CommonApi\Query\ModelInterface;
use CommonApi\Query\QueryBuilderInterface;

/**
 * Event Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Event extends Query implements TriggerInterface
{
    /**
     * Schedule Event Callback
     *
     * @var    callable
     * @since  1.0
     */
    protected $schedule_event;

    /**
     * Parameters
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * Event Name
     *
     * @var    string
     * @since  1.0
     */
    protected $event_name;

    /**
     * Class Constructor
     *
     * @param  ModelInterface        $model
     * @param  array                 $runtime_data
     * @param  QueryBuilderInterface $query
     * @param  callable              $schedule_event
     *
     * @since  1.0
     */
    public function __construct(
        ModelInterface $model = null,
        $runtime_data = array(),
        QueryBuilderInterface $query = null,
        callable $schedule_event = null
    ) {
        parent::__construct(
            $model,
            $runtime_data,
            $query
        );

        $this->schedule_event = $schedule_event;
    }

    /**
     * Trigger Event Method
     *
     * @param   string $event_name
     *
     * @return  array
     * @since   1.0.0
     */
    public function triggerEvent($event_name)
    {
        if ($this->query->getModelRegistry('process_events') === 0) {
            return $this;
        }

        $this->event_name = $event_name;
        $options          = $this->prepareEventInput();
        $schedule_event   = $this->schedule_event;
        $results          = $schedule_event($this->event_name, $options);

        $this->processEventResults($results);

        return $this;
    }

    /**
     * Prepare Event Input
     *
     * @param   string $event_name
     *
     * @return  array
     * @since   1.0.0
     */
    protected function prepareEventInput()
    {
        $options                  = array();
        $options['parameters']    = $this->parameters;
        $options['row']           = $this->row;
        $options['query']         = $this->query;
        $options['query_results'] = $this->query_results;
        $options['rendered_view'] = null;
        $options['rendered_page'] = null;

        $options['model_registry'] = $this->getModelRegistry();

        return $options;
    }

    /**
     * Process Event Results
     *
     * @param   array $results
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processEventResults($results)
    {
        $this->parameters    = $results['parameters'];
        $this->row           = $results['row'];
        $this->query         = $results['query'];
        $this->query_results = $results['query_results'];

        $this->setModelRegistry(null, $results['model_registry']);

        return $this;
    }
}
