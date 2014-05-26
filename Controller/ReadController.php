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
use CommonApi\Exception\RuntimeException;

/**
 * Read Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ReadController extends ModelRegistryQueryController implements ReadControllerInterface
{
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

        if (trim($this->sql) == '') {
            $this->setModelRegistrySQL();
            $this->sql = $this->getSQL();
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
        //todo cache

        $this->executeQuery();

        if ($this->query_object === 'result' || $this->query_object === 'distinct') {
            return $this;
        }

        if ($this->use_pagination === 0
            || (int)$this->total === 0
        ) {
            return $this->total;
        }

        $this->processPagination();

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
     * @return  $this
     * @since   1.0
     */
    protected function processPagination()
    {
        $this->offset_count  = 0;
        $query_results = array();
        $process_rows_count = 0;

        foreach ($this->query_results as $item) {

            /** Previous Data: Read past offset */
            if ($this->offset_count < $this->offset) {
                $this->offset_count++;

                /** Current Data: Collect this data for display */
            } elseif ($process_rows_count < $this->count) {
                $query_results = $item;
                $process_rows_count++;

                /** Next Data: Offset and Results set collected. Exit. */
            } else {
                break;
            }
        }

        $this->query_results = $query_results;

        return $this;
    }

    /**
     * Schedule onBeforeRead Event
     *
     * - Model Query has been developed and is passed into the event, along with runtime_data and registry data
     *
     * - Good event for modifying selection criteria, like adding tag selectivity, or setting publishing criteria
     *
     * - Examples: Publishedstatus
     *
     * @return  $this
     * @since   1.0
     */
    public function triggerOnBeforeReadEvent()
    {
        if ($this->getModelRegistry('process_event') == 0) {
            return $this;
        }

        $schedule_event = $this->schedule_event;

        $options                   = array();
        $options['runtime_data']   = $this->runtime_data;
        $options['plugin_data']    = $this->plugin_data;
        $options['query']          = $this->query;
        $options['model_registry'] = $this->model_registry;
        $options['rendered_view']  = null;
        $options['rendered_page']  = null;
        $options['query_results']  = null;
        $options['row']            = null;
        $options['parameters']     = null;

        $results = $schedule_event($event_name = 'onBeforeRead', $options);

        if (is_array($results)) {

            if (isset($results['runtime_data'])) {
                $this->runtime_data = $results['runtime_data'];
            }
            if (isset($results['plugin_data'])) {
                $this->runtime_data = $results['plugin_data'];
            }
            if (isset($results['query'])) {
                $this->query = $results['query'];
            }
            if (isset($results['model_registry'])) {
                $this->model_registry = $results['model_registry'];
            }
        }

        return $this;
    }

    /**
     * Schedule Event onAfterRead Event
     *
     * @return  $this
     * @since   1.0
     */
    public function triggerOnAfterReadEvent()
    {
        if ($this->getModelRegistry('process_events') == 0) {
            return $this;
        }

        $schedule_event = $this->schedule_event;

        $rows                = $this->query_results;
        $this->query_results = array();

        $first = true;

        if (count($rows) == 0) {
        } else {

            $i = 0;
            foreach ($rows as $row) {

                $this->runtime_data->first = $first;

                $options                   = array();
                $options['runtime_data']   = $this->runtime_data;
                $options['plugin_data']    = $this->plugin_data;
                $options['query']          = $this->query;
                $options['model_registry'] = $this->model_registry;
                $options['rendered_view']  = null;
                $options['rendered_page']  = null;
                $options['query_results']  = null;
                $options['row']            = $row;
                $options['parameters']     = $row->parameters;

                $results = $schedule_event($event_name = 'onAfterRead', $options);

                if (is_array($results)) {

                    if (isset($results['runtime_data'])) {
                        $this->runtime_data = $results['runtime_data'];
                    }
                    if (isset($results['plugin_data'])) {
                        $this->runtime_data = $results['plugin_data'];
                    }
                    if (isset($results['query'])) {
                        $this->query = $results['query'];
                    }
                    if (isset($results['row'])) {
                        $row = $results['row'];
                    }
                    if (isset($results['parameters'])) {
                        // already in $row->parameters
                        $parameters = $results['parameters'];
                        if (count($parameters) > 0 && is_array($parameters)) {
                            $row->parameters = $parameters;
                        }
                    }
                    if (isset($results['model_registry'])) {
                        $this->model_registry = $results['model_registry'];
                    }
                } else {
                    break;
                };
                $first = false;
            }
        }

        unset($this->runtime_data->first);

        $this->query_results = $rows;

        return $this;
    }

    /**
     * Schedule Event onAfterRead Event
     *
     * @return  $this
     * @since   1.0
     */
    public function triggerOnAfterReadallEvent()
    {
        if ($this->getModelRegistry('process_events') == 0) {
            return $this;
        }

        $schedule_event = $this->schedule_event;

        $options                   = array();
        $options['runtime_data']   = $this->runtime_data;
        $options['plugin_data']    = $this->plugin_data;
        $options['query']          = $this->query;
        $options['model_registry'] = $this->model_registry;
        $options['rendered_view']  = null;
        $options['rendered_page']  = null;
        $options['query']          = $this->query;
        $options['query_results']  = $this->query_results;
        $options['row']            = null;
        $options['parameters']     = null;

        $results = $schedule_event($event_name = 'onAfterReadall', $options);

        if (is_array($results)) {

            if (isset($results['runtime_data'])) {
                $this->runtime_data = $results['runtime_data'];
            }
            if (isset($results['plugin_data'])) {
                $this->runtime_data = $results['plugin_data'];
            }
            if (isset($results['query'])) {
                $this->query = $results['query'];
            }
            if (isset($results['query_results'])) {
                $this->query_results = $results['query_results'];
            }
            if (isset($results['model_registry'])) {
                $this->model_registry = $results['model_registry'];
            }
        }

        return $this;
    }

    /**
     * Schedule Event onAfterRead Event
     *
     * @return  array
     * @since   1.0
     */
    protected function returnQueryResults()
    {
        if ($this->query_object == 'result'
            || $this->query_object == 'distinct'
        ) {
            return $this->query_results;
        }

        if (count($this->query_results) === 0
            || $this->query_results === false
            || !is_array($this->query_results)
        ) {
            $this->query_results = array();
        }

        if ($this->query_object == 'item') {
            $result              = $this->query_results[0];
            $this->query_results = $result;
        }

        return $this->query_results;
    }
}
