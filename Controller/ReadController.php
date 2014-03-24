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
 * @since      1.0
 */
class ReadController extends Controller implements ReadControllerInterface
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
        if ($this->model->getModelRegistry('data_object') == 'Database') {
            $this->model->setQuery($this->sql);
            $this->triggerOnBeforeReadEvent();
            $this->runQuery();
        } else {
            $this->getDataNonDatabase();
        }

        $this->triggerOnAfterReadEvent();

        $this->triggerOnAfterReadallEvent();

        if ($this->model->getModelRegistry('data_object') == 'Database') {
        } else {
            return $this->query_results;
        }

        if ($this->model->getModelRegistry('query_object') == 'result'
            || $this->model->getModelRegistry('query_object') == 'distinct'
        ) {
            return $this->query_results;
        }

        if (count($this->query_results) === 0
            || $this->query_results === false
        ) {
            return array();
        }

        if (is_array($this->query_results)) {
        } else {
            $this->query_results = array();
        }

        if ($this->model->getModelRegistry('query_object') == 'item') {
            $result              = $this->query_results[0];
            $this->query_results = $result;
        }

        return $this->query_results;
    }

    /**
     * Execute data retrieval query for standard requests
     *
     * @return  $this
     * @since   1.0
     */
    protected function runQuery()
    {
        $this->runtime_data->pagination_total = (int)$this->model->getData();

        $this->query_results = $this->model->get('query_results');

        if ($this->model->getModelRegistry('query_object') == 'result'
            || $this->model->getModelRegistry('query_object') == 'distinct'
        ) {
            return $this;
        }

        return $this;
    }

    /**
     * Retrieve Data from a Non-database datasource
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getDataNonDatabase()
    {
        if ($this->getModelRegistry('data_object') == 'Datalist') {
            $this->query_results = $this->getModelRegistry('values');
            return $this;
        }
        echo 'In getDataNonDatabase <br />';
        echo '<pre>';
        var_dump($this->model->model_registry);
        echo '</pre>';
        if (strtolower($this->model->getModelRegistry('model_name')) == 'dummy') {
            $this->query_results = array();
            return $this;
        }

        $class              = $this->model->getModelRegistry('class');
        $class_query_method = $this->model->getModelRegistry('class_query_method');

        if ($this->model->getModelRegistry('model_name') == 'Primary') {
            $method_parameter = 'Data';
        } elseif ($this->model->getModelRegistry('class_query_method_parameter') == 'Template') {
            $method_parameter = $this->runtime_data->template_view_path_node;
        } elseif ($this->model->getModelRegistry('class_query_method_parameter') == 'Model') {
            $method_parameter = $this->model->getModelRegistry('model_name');
        } else {
            $method_parameter = $this->model->getModelRegistry('class_query_method_parameter');
        }

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
        if ($this->model->getModelRegistry('process_event') == 0) {
            return $this;
        }

        $schedule_event = $this->scheduleEvent;

        $options                   = array();
        $options['runtime_data']   = $this->runtime_data;
        $options['plugin_data']    = $this->plugin_data;
        $options['query']          = $this->model->query;
        $options['model_registry'] = $this->model->model_registry;
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
                $this->model->query = $results['query'];
            }
            if (isset($results['model_registry'])) {
                $this->model->model_registry = $results['model_registry'];
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
//echo '<br /><br />';
//echo $this->model->getQueryString();
//echo '<br /><br />';

        if ($this->getModelRegistry('process_events', 1) == 0) {
            return $this;
        }

        $schedule_event = $this->scheduleEvent;

        $rows                = $this->query_results;
        $this->query_results = array();

        $first = true;

        if (count($rows) == 0) {
        } else {
            foreach ($rows as $row) {

                $this->runtime_data->first = $first;

                $options                   = array();
                $options['runtime_data']   = $this->runtime_data;
                $options['plugin_data']    = $this->plugin_data;
                $options['query']          = $this->model->query;
                $options['model_registry'] = $this->model->model_registry;
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
                        $this->model->query = $results['query'];
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
                        $this->model->model_registry = $results['model_registry'];
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
        if ($this->getModelRegistry('process_events', 1) == 0) {
            return $this;
        }

        $schedule_event = $this->scheduleEvent;

        $options                   = array();
        $options['runtime_data']   = $this->runtime_data;
        $options['plugin_data']    = $this->plugin_data;
        $options['query']          = $this->model->query;
        $options['model_registry'] = $this->model->model_registry;
        $options['rendered_view']  = null;
        $options['rendered_page']  = null;
        $options['query']          = $this->model->get('query');
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
                $this->model->query = $results['query'];
            }
            if (isset($results['query_results'])) {
                $this->query_results = $results['query_results'];
            }
            if (isset($results['model_registry'])) {
                $this->model->model_registry = $results['model_registry'];
            }
        }

        return $this;
    }
}
