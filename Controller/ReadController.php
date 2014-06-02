<?php
/**
 * Read Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Controller\ReadControllerInterface;

/**
 * Read Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ReadController extends QueryController implements ReadControllerInterface
{
    /**
     * Process Rows Count
     *
     * @var    integer
     * @since  1.0
     */
    protected $process_rows_count;

    /**
     * Parameters
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * Method to get retrieve data
     *
     * @return  mixed
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getData()
    {
        $this->triggerOnBeforeReadEvent();

        if (trim($this->sql) === '') {
            $this->sql = $this->getSql();
        }

        $this->runQuery();

        $this->triggerOnAfterReadEvent();

        $this->triggerOnAfterReadallEvent();

        return $this->returnQueryResults();
    }

    /**
     * Build the SQL needed for the query
     *
     * @return  $this
     * @since   1.0
     */
    protected function runQuery()
    {
        $this->executeQuery();

        if ($this->use_pagination === 0
            || (int)$this->total === 0
        ) {
            return $this->total;
        }

        $this->processPagination($this->query_results);

        return $this;
    }

    /**
     * Execute the Query
     *
     * @return  $this
     * @since   1.0
     */
    protected function executeQuery()
    {
        $this->query_results = $this->model->getData($this->query_object, $this->sql);
        $this->total         = count($this->query_results);

        if ($this->offset > $this->total) {
            $this->offset = 0;
        }
    }

    /**
     * Process Pagination Requirements
     *
     * @param   array $query_results
     *
     * @return  $this
     * @since   1.0
     */
    protected function processPagination($query_results)
    {
        $this->offset_count       = 0;
        $this->query_results      = array();
        $this->process_rows_count = 0;

        foreach ($query_results as $item) {
            $complete = $this->processPaginationItem($item);
            if ($complete === true) {
                break;
            }
        }

        return $this;
    }

    /**
     * Process Pagination Item
     *
     * @param   object $item
     *
     * @return  boolean
     * @since   1.0
     */
    protected function processPaginationItem($item)
    {
        $complete = false;

        /** Previous Data: Read past offset */
        if ($this->offset_count < $this->offset) {
            $this->offset_count++;

            /** Current Data: Collect this data for display */
        } elseif ($this->process_rows_count < $this->count) {
            $this->query_results[] = $item;
            $this->process_rows_count++;

            /** Next Data: Offset and Results set collected. Exit. */
        } else {
            $complete = true;
        }

        return $complete;
    }

    /**
     * Schedule Event onAfterRead Event
     *
     * @return  array
     * @since   1.0
     */
    protected function returnQueryResults()
    {
        if ($this->query_object === 'result'
            || $this->query_object === 'distinct'
        ) {
            return $this->query_results;
        }

        if ($this->query_object === 'item') {
            $result              = $this->query_results[0];
            $this->query_results = $result;
        }

        return $this->query_results;
    }

    /**
     * Schedule onBeforeRead Event
     *
     * @return  $this
     * @since   1.0
     */
    public function triggerOnBeforeReadEvent()
    {
        return $this->triggerEvent('onBeforeRead');
    }

    /**
     * Schedule onAfterRead Event
     *
     * @return  $this
     * @since   1.0
     */
    public function triggerOnAfterReadEvent()
    {
        $rows                = $this->query_results;
        $this->query_results = array();
        $first               = true;

        if (count($rows) === 0) {
        } else {
            foreach ($rows as $this->row) {
                $this->runtime_data->first = $first;
                $this->triggerEvent('onAfterRead');
                $first = false;
            }
        }

        unset($this->runtime_data->first);
        $this->query_results = $rows;

        return $this;
    }

    /**
     * Schedule Event onAfterReadAll Event
     *
     * @return  $this
     * @since   1.0
     */
    public function triggerOnAfterReadallEvent()
    {
        return $this->triggerEvent('onAfterReadall');
    }

    /**
     * Trigger Event
     *
     * @param  string $event_name
     *
     * @return  ReadController
     * @since   1.0
     */
    protected function triggerEvent($event_name)
    {
        if ($this->getModelRegistry('process_event') === 0) {
            return $this;
        }

        $schedule_event = $this->schedule_event;
        $options        = $this->prepareEventInput();
        $results        = $schedule_event($event_name, $options);

        $this->processEventResults($results);

        return $this;
    }

    /**
     * Prepare Event Input
     *
     * @return  array
     * @since   1.0
     */
    protected function prepareEventInput()
    {
        $options                   = array();
        $options['runtime_data']   = $this->runtime_data;
        $options['plugin_data']    = $this->plugin_data;
        $options['query']          = $this->query;
        $options['query_results']  = $this->query_results;
        $options['row']            = $this->row;
        $options['parameters']     = $this->row->parameters;
        $options['model_registry'] = $this->model_registry;
        $options['rendered_view']  = null;
        $options['rendered_page']  = null;

        return $options;
    }

    /**
     * Process Event Results
     *
     * @param   array $results
     *
     * @return  $this
     * @since   1.0
     */
    protected function processEventResults($results)
    {
        $this->runtime_data   = $results['runtime_data'];
        $this->plugin_data    = $results['plugin_data'];
        $this->query          = $results['query'];
        $this->query_results  = $results['query_results'];
        $this->row            = $results['row'];
        $this->parameters     = $results['parameters'];
        $this->model_registry = $results['model_registry'];

        return $this;
    }
}
