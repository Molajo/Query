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
class ReadController extends QueryController implements ReadControllerInterface
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

        if ($this->getModelRegistry('query_object') == 'result'
            || $this->getModelRegistry('query_object') == 'distinct'
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

        if ($this->getModelRegistry('query_object') == 'item') {
            $result              = $this->query_results[0];
            $this->query_results = $result;
        }

        return $this->query_results;
    }

    /**
     * Based on Model Registry, set default SELECT, FROM and WHERE clauses for query
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelRegistrySQL()
    {
        $select = $this->query->get('columns', array());

        if (count($select) == 0) {
            $this->setSelectColumns();
        }

        $from = $this->query->get('from', array());

        if (count($from) == 0) {
            $this->setFromTable();
        }

        $where = $this->query->get('where', array());

        if (count($where) == 0) {
            $this->setWhereStatements();
        }

        $this->setSpecialJoins();
        $this->setModelRegistryCriteria();
        $this->setModelRegistryCriteriaArrayCriteria();

        return $this;
    }

    /**
     * Uses joins defined in model registry to build SQL statements
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setSelectColumns()
    {

//if ($this->model_registry['query_object'] == 'distinct') {
//            $this->query->setDistinct(true);
//        }

        if ($this->model_registry['query_object'] == 'result') {

            if ((int)$this->model_registry['id'] > 0) {

                $this->query->select($this->model_registry['primary_prefix'] . '.' . $this->model_registry['name_key']);

                return $this;
            }

            $this->query->select($this->model_registry['primary_prefix'] . '.' . $this->model_registry['primary_key']);

            return $this;
        }

        if (count($this->model_registry['columns']) === 0) {
            $this->query->select($this->model_registry['primary_prefix'] . '.' . '*');
        } else {
            foreach ($this->model_registry['columns'] as $column) {
                $this->query->select($this->model_registry['primary_prefix'] . '.' . $column['name']);
            }
        }

        return $this;
    }

    /**
     * Set From Table Value
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setFromTable()
    {
        $primary_prefix = $this->model_registry['primary_prefix'];
        $table_name     = $this->model_registry['table_name'];

        $this->query->from($table_name, $primary_prefix);

        return $this;
    }

    /**
     * Set Where Statements
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setWhereStatements()
    {
        if ((int)$this->model_registry['id'] > 0) {
            $this->query->where(
                'column',
                $this->model_registry['primary_prefix'] . '.' . $this->model_registry['primary_key'],
                '=',
                'integer',
                $this->model_registry['primary_key_value']
            );

        } elseif (trim($this->model_registry['name_key_value']) == '') {

        } else {
            $this->query->where(
                'column',
                $this->model_registry['primary_prefix'] . '.' . $this->model_registry['name_key'],
                '=',
                'string',
                $this->model_registry['name_key_value']
            );
        }

        return $this;
    }

    /**
     * Uses joins defined in model registry to build SQL statements
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setSpecialJoins()
    {
        if ($this->model_registry['use_special_joins'] === 0
            || count($this->model_registry['joins']) === 0
        ) {
            return $this;
        }

        $joins = $this->model_registry['joins'];

        foreach ($joins as $join) {

            $join_table = $join['table_name'];
            $alias      = $join['alias'];
            $select     = $join['select'];
            $join_to    = $join['jointo'];
            $join_with  = $join['joinwith'];

            /* Select fields */
            if (trim($select) == '') {
                $select_array = array();
            } else {
                $select_array = explode(',', $select);
            }

            if ($this->model_registry['query_object'] == 'result') {
            } else {

                if (count($select_array) > 0) {
                    foreach ($select_array as $select_item) {
                        $this->query->select(
                            trim($alias) . '.' . trim($select_item),
                            trim($alias) . '_' . trim($select_item)
                        );
                    }
                }
            }

            /* Join Tables */
            $join_to_array     = explode(',', $join_to);
            $join_with_array   = explode(',', $join_with);
            $where_left        = null;
            $where_left_alias  = $alias;
            $where_right       = null;
            $where_right_alias = $this->model_registry['primary_prefix'];

            if (count($join_to_array) > 0) {

                $i = 0;
                foreach ($join_to_array as $join_to_item) {

                    /** where THIS operator that */
                    $results           = $this->setWhereElement($join_to_item, $where_left_alias);
                    $where_left_filter = $results[0];
                    $where_left        = $results[1];

                    /** where this OPERATOR that */
                    $with = $join_with_array[$i];

                    $operator = '=';
                    if (substr($with, 0, 2) == '>=') {
                        $operator = '>=';
                        $with     = substr($with, 2, strlen($with) - 2);

                    } elseif (substr($with, 0, 1) == '>') {
                        $operator = '>';
                        $with     = substr($with, 0, strlen($with) - 1);

                    } elseif (substr($with, 0, 2) == '<=') {
                        $operator = '<=';
                        $with     = substr($with, 2, strlen($with) - 2);

                    } elseif (substr($with, 0, 1) == '<') {
                        $operator = '<';
                        $with     = substr($with, 0, strlen($with) - 1);
                    }

                    /** where this operator THAT */
                    $results            = $this->setWhereElement($with, $where_right_alias);
                    $where_right_filter = $results[0];
                    $where_right        = $results[1];

                    /** put the where together */
                    if ($where_left === null || $where_right === null) {
                    } else {

                        if ($join_table === null) {
                        } else {
                            $this->query->from($join_table, $alias);
                            $join_table = null;
                        }

                        $this->query->where(
                            $where_left_filter,
                            $where_left,
                            $operator,
                            $where_right_filter,
                            $where_right
                        );
                    }

                    $i ++;
                }
            }
        }

        return $this;
    }

    /**
     * Add Model Registry Criteria to Query
     *
     * @param   string $join_item
     *
     * @return  array
     * @since   1.0
     */
    protected function setWhereElement($join_item, $alias)
    {
        $filter     = 'column';
        $where_part = null;

        if ($join_item == 'APPLICATION_ID') {
            if ((int)$this->application_id === 0) {
            } else {
                $where_part = $this->application_id;
                $filter     = 'integer';
            }

        } elseif ($join_item == 'SITE_ID') {
            if ((int)$this->site_id === 0) {
            } else {
                $where_part = $this->site_id;
                $filter     = 'integer';
            }

        } elseif ($join_item == 'MENU_ID') {
            $where_part = (int)$this->model_registry['criteria_menu_id'];
            $filter     = 'integer';

        } elseif ($join_item == 'CATALOG_TYPE_ID') {
            $where_part = (int)$this->model_registry['catalog_type_id'];
            $filter     = 'integer';

        } elseif (is_numeric($join_item)) {
            $where_part = (int)$join_item;
            $filter     = 'integer';

        } else {

            $has_alias = explode('.', $join_item);

            if (count($has_alias) > 1) {
                $to_join = trim($has_alias[0]) . '.' . trim($has_alias[1]);
            } else {
                $to_join = trim($alias) . '.' . trim($join_item);
            }

            $where_part = $to_join;
        }

        return array($filter, $where_part);
    }

    /**
     * Uses joins defined in model registry to build SQL statements
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModelRegistryCriteria()
    {
        if ($this->model_registry['criteria_status'] === '') {
        } else {
            $this->query->where(
                'column',
                $this->model_registry['primary_prefix'] . '.' . 'status',
                'IN',
                'integer',
                $this->model_registry['criteria_status']
            );
        }

        if ((int)$this->model_registry['criteria_catalog_type_id'] === 0) {
        } else {
            $this->query->where(
                'column',
                $this->model_registry['primary_prefix'] . '.' . 'catalog_type_id',
                '=',
                'integer',
                (int)$this->model_registry['criteria_catalog_type_id']
            );
        }

        if ((int)$this->model_registry['criteria_extension_instance_id'] === 0) {
        } else {
            $this->query->where(
                'column',
                $this->model_registry['primary_prefix'] . '.' . 'extension_instance_id',
                '=',
                'integer',
                (int)$this->model_registry['criteria_extension_instance_id']
            );
        }

        if ((int)$this->model_registry['criteria_menu_id'] === 0) {
        } else {
            $this->query->where(
                'column',
                $this->model_registry['primary_prefix'] . '.' . 'menu_id',
                '=',
                'integer',
                (int)$this->model_registry['criteria_menu_id']
            );
        }

        return $this;
    }

    /**
     * Set Criteria Statements
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModelRegistryCriteriaArrayCriteria()
    {
        if (count($this->model_registry['criteria_array']) > 0) {
        } else {
            return $this;
        }

        foreach ($this->model_registry['criteria_array'] as $item) {

            if (isset($item['value'])) {
                $this->query->where('column', $item['name'], $item['connector'], 'integer', $item['value']);

            } elseif (isset($item['name2'])) {
                $this->query->where('column', $item['name'], $item['connector'], 'column', $item['name2']);
            }
        }

        return $this;
    }

    /**
     * Set Model Registry Limits
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModelRegistryLimits()
    {
        if (count($this->model_registry['use_pagination']) == 0) {
        } else {
            return $this;
        }

        $this->query->setOffsetAndLimit(
            $this->model_registry['offset'],
            $this->model_registry['limit']
        );

        return $this;
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
//            $cache_key = $this->query->__toString();
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
                $this->query->clear('select');
                $this->query->select('count(*)');
                $this->model_registry['total_items'] = $this->database->loadResult($this->query->getSQL());
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
                $offset_count ++;
                /** Collect next set for pagination */
            } elseif ($results_count < $count) {
                $this->query_results[] = $item;
                $results_count ++;
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

        $schedule_event = $this->scheduleEvent;

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

        $schedule_event = $this->scheduleEvent;

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

        $schedule_event = $this->scheduleEvent;

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
}
