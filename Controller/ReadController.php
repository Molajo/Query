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
        $this->query_results = $this->model->getData(
            $this->getModelRegistry('query_object'),
            $this->sql
        );

        if ($this->getModelRegistry('query_object') == 'result'
            || $this->getModelRegistry('query_object') == 'distinct'
        ) {
            return $this;
        }

        return $this;

//        if (is_object($this->cache)) {
//            $cache_key = $this->__toString();
//            $results   = $this->cache->get(serialize($cache_key));
//            if ($results->isHit() === true) {
//                $cached_output = $results->value;
//            } else {
        //               $cached_output = false;
        //           }
        //      } else {
        $cached_output = false;
        //    }

        if ($cached_output === false) {

            if ($query_object == 'result') {
                $query_results = $this->database->loadResult($this->sql);
            } else {
                $query_results = $this->database->loadObjectList($this->sql);
            }

            if ($count < count($query_results)) {
                $hold = $this->query;
                $this->clear('select');
                $this->select('count(*)');
                $this->model_registry['total_items'] = $this->database->loadResult($this->getSQL());
                $this->query                         = $hold;
            } else {
                $this->model_registry['total_items'] = count($query_results);
            }

//            if (is_object($this->cache)) {
//                $this->cache->set('Query', $cache_key, $query_results);
//            }
        } else {
            $query_results = $cached_output;
        }

        $total = count($query_results);

        if ($offset > $total) {
            $offset = 0;
        }

        if ($use_pagination === 0
            || (int)$total === 0
        ) {
            $this->query_results = $query_results;

            return $total;
        }

        $offset_count  = 0;
        $results_count = 0;

        foreach ($query_results as $item) {

            /** Read past offset */
            if ($offset_count < $offset) {
                $offset_count++;
                /** Collect next set for pagination */
            } elseif ($results_count < $count) {
                $this->query_results[] = $item;
                $results_count++;
                /** Offset and Results set collected. Exit. */
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
        if ($this->getModelRegistry('query_object') == 'result'
            || $this->getModelRegistry('query_object') == 'distinct'
        ) {
            return $this->query_results;
        }

        if (count($this->query_results) === 0
            || $this->query_results === false
            || !is_array($this->query_results)
        ) {
            $this->query_results = array();
        }

        if ($this->getModelRegistry('query_object') == 'item') {
            $result              = $this->query_results[0];
            $this->query_results = $result;
        }

        return $this->query_results;
    }
}
