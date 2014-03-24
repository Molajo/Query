<?php
/**
 * Read Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Model;

use CommonApi\Model\ReadModelInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Read Model
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ReadModel extends Model implements ReadModelInterface
{
    /**
     * Prepare query object for standard database queries
     *
     * @return  $this
     * @since   1.0
     */
    public function setQuery($sql = '')
    {
//        if ($sql === '') {
        $this->setBaseQuery();
        $this->useSpecialJoins();
        $this->setModelCriteria();

//        } else {
//            $this->query->select = $sql;
//        }
//echo '<br /><br />';
//echo $this->getQueryString();
//echo '<br /><br />';
        return $this;
    }

    /**
     * Execute query and returns results
     *
     * @return  object
     * @since   1.0
     */
    public function getData()
    {
        if (isset($this->model_registry['primary_prefix'])) {
            $primary_prefix = $this->model_registry['primary_prefix'];
        } else {
            $primary_prefix = 'a';
        }

        if (isset($this->model_registry['query_object'])) {
            $query_object = $this->model_registry['query_object'];
        } else {
            $query_object = 'list';
        }

        if ($query_object == 'result'
            || $query_object == 'item'
            || $query_object == 'list'
            || $query_object == 'distinct'
        ) {
        } else {
            $query_object = 'list';
        }

        if (isset($this->model_registry['model_offset'])) {
            $offset = $this->model_registry['model_offset'];
        } else {
            $offset = 0;
        }

        if (isset($this->model_registry['model_count'])) {
            $count = $this->model_registry['model_count'];
        } else {
            $count = 10;
        }

        if (isset($this->model_registry['use_pagination'])) {
            $use_pagination = $this->model_registry['use_pagination'];
        } else {
            $use_pagination = 0;
        }

        $this->query_results = array();

        if ($offset == 0 && $count == 0) {

            if ($query_object == 'result') {
                $offset         = 0;
                $count          = 1;
                $use_pagination = 0;

            } elseif ($query_object == 'distinct') {
                $offset         = 0;
                $count          = 999999;
                $use_pagination = 0;

            } else {
                $offset         = 0;
                $count          = 10;
                $use_pagination = 1;
            }
        }

//echo '<br /><br />';
//echo $this->getQueryString();
//echo '<br /><br />';

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

        if ($query_object == 'list') {
        } else {
            $use_pagination = 0;
        }

        if ($cached_output === false) {

            if ((int)$use_pagination === 0) {
                $offset = 0;
                $count  = 99999999;
            }

            if ($query_object == 'result') {
                $query_results = $this->database->loadResult($this->query->getSQL());
            } else {
                $query_results = $this->database->loadObjectList($offset, $count);
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

        return $results_count;
    }

    /**
     * Based on Model Registry, set default SELECT, FROM and WHERE clauses for query
     *
     * @return  $this
     * @since   1.0
     */
    protected function setBaseQuery()
    {
        $key = 0;

        if (isset($this->model_registry['primary_key_value'])) {
            $key = (int)$this->model_registry['primary_key_value'];
        }

        if ($key === 0) {
            if (isset($this->model_registry['criteria_source_id'])) {
                $key = (int)$this->model_registry['criteria_source_id'];
            }
        }

        if ($key === 0) {
            $this->model_registry['primary_key_value'] = null;
        } else {
            $this->model_registry['primary_key_value'] = $key;
        }

        if (isset($this->model_registry['fields'])) {
            $columns = $this->model_registry['fields'];
        } else {
            $columns = array();
        }

        if (isset($this->model_registry['table_name'])) {
            $table_name = $this->model_registry['table_name'];
        } else {
            $table_name = '#__content';
        }

        if (isset($this->model_registry['primary_prefix'])) {
            $primary_prefix = $this->model_registry['primary_prefix'];
        } else {
            $primary_prefix = 'a';
        }

        if (isset($this->model_registry['primary_key'])) {
            $primary_key = $this->model_registry['primary_key'];
        } else {
            $primary_key = 'id';
        }

        if (isset($this->model_registry['primary_key_value'])) {
            $id = $this->model_registry['primary_key_value'];
        } else {
            $id = null;
        }

        if (isset($this->model_registry['name_key'])) {
            $name_key = $this->model_registry['name_key'];
        } else {
            $name_key = 'title';
        }

        if (isset($this->model_registry['name_key_value'])) {
            $name_key_value = $this->model_registry['name_key_value'];
        } else {
            $name_key_value = null;
        }

        if (isset($this->model_registry['query_object'])) {
            $query_object = $this->model_registry['query_object'];
        } else {
            $query_object = 'list';
        }

        if ($query_object == 'result'
            || $query_object == 'item'
            || $query_object == 'list'
            || $query_object == 'distinct'
        ) {
        } else {
            $query_object = 'list';
        }

        if (isset($this->model_registry['criteria_array'])) {
            $criteria_array = $this->model_registry['criteria_array'];
        } else {
            $criteria_array = array();
        }

        if ($this->query->select == null) {

            if ($query_object == 'result') {

                if ((int)$id > 0) {

                    $this->query->select(
                        ' '
                        . $primary_prefix
                        . '.'
                        . $name_key
                        . ' '
                    );

                    $this->query->where(
                        $primary_prefix
                        . '.'
                        . $primary_key
                        . ' = '
                        . $id
                    );

                } else {

                    $this->query->select(
                        $primary_prefix
                        . '.'
                        . $primary_key
                    );

                    $this->query->where(
                        $primary_prefix
                        . '.'
                        . $name_key
                        . ' = '
                        . $name_key_value
                    );
                }

            } else {

                $first = true;

                if (count($columns) == 0) {
                    $this->query->select(
                        $primary_prefix
                        . '.'
                        . '*'
                    );

                } else {
                    foreach ($columns as $column) {

                        if ($first === true && strtolower(trim($query_object)) == 'distinct') {
                            $first = false;
                            $this->query->select(
                                'DISTINCT '
                                . $primary_prefix
                                . '.'
                                . $column['name']
                            );
                        } else {
                            $this->query->select(
                                $primary_prefix
                                . '.'
                                . $column['name']
                            );
                        }
                    }
                }
            }
        }

        if ($this->query->from == null) {
            $this->query->from(
                $table_name
                . ' as '
                . $primary_prefix
            );
        }

        if ($this->query->where == null) {
            if ((int)$id > 0) {
                $this->query->where(
                    $primary_prefix
                    . '.'
                    . $primary_key
                    . ' = '
                    . $id
                );
            } elseif (trim($name_key_value) == '') {
            } else {
                $this->query->where(
                    $primary_prefix
                    . '.'
                    . $name_key
                    . ' = '
                    . $name_key_value
                );
            }
        }

        if (is_array($criteria_array) && count($criteria_array) > 0) {

            foreach ($criteria_array as $item) {

                if (isset($item['value'])) {
                    $this->query->where(
                        $item['name']
                        . ' '
                        . $item['connector']
                        . ' '
                        . ($item['value'])
                    );
                } elseif (isset($item['name2'])) {
                    $this->query->where(
                        $item['name']
                        . ' '
                        . $item['connector']
                        . ' '
                        . $item['name2']
                    );
                }
            }
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
    protected function useSpecialJoins()
    {
        if (isset($this->model_registry['use_special_joins'])) {
            $use_special_joins = $this->model_registry['use_special_joins'];
        } else {
            $use_special_joins = 1;
        }

        if ((int)$use_special_joins == 0) {
            return $this;
        }

        if (isset($this->model_registry['joins'])) {
            $joins = $this->model_registry['joins'];
        } else {
            $joins = array();
        }

        if (count($joins) === 0) {
            return $this;
        }

        if (isset($this->model_registry['primary_prefix'])) {
            $primary_prefix = $this->model_registry['primary_prefix'];
        } else {
            $primary_prefix = 'a';
        }

        if (isset($this->model_registry['query_object'])) {
            $query_object = $this->model_registry['query_object'];
        } else {
            $query_object = 'list';
        }

        if ($query_object == 'result'
            || $query_object == 'item'
            || $query_object == 'list'
            || $query_object == 'distinct'
        ) {
        } else {
            $query_object = 'list';
        }

        if (isset($this->model_registry['menu_id'])) {
            $menu_id = $this->model_registry['menu_id'];
        } else {
            $menu_id = '';
        }

        if (isset($this->model_registry['catalog_type_id'])) {
            $catalog_type_id = $this->model_registry['catalog_type_id'];
        } else {
            $catalog_type_id = '';
        }

        foreach ($joins as $join) {

            $join_table = $join['table_name'];
            $alias      = $join['alias'];
            $select     = $join['select'];
            $join_to    = $join['jointo'];
            $join_with  = $join['joinwith'];

            $this->query->from(
                $join_table
                . ' as '
                . $alias
            );

            /* Select fields */
            if (trim($select) == '') {
                $select_array = array();
            } else {
                $select_array = explode(',', $select);
            }

            if ($query_object == 'result') {
            } else {

                if (count($select_array) > 0) {

                    foreach ($select_array as $select_item) {

                        $this->query->select(
                            trim($alias)
                            . '.'
                            . trim($select_item)
                            . ' as '
                            . trim($alias) . '_' . trim($select_item)
                        );
                    }
                }
            }

            /* Join Fields */
            $join_to_array   = explode(',', $join_to);
            $join_with_array = explode(',', $join_with);
            $where_left      = null;
            $where_right     = null;

            if (count($join_to_array) > 0) {

                $i = 0;
                foreach ($join_to_array as $join_to_item) {

                    /** join THIS to that */
                    $to = $join_to_item;

                    if ($to == 'APPLICATION_ID') {
                        if ((int)$this->application_id === 0) {
                            $where_left = null;
                            $to         = null;
                        } else {
                            $where_left = $this->application_id;
                        }

                    } elseif ($to == 'SITE_ID') {
                        $where_left = $this->site_id;

                    } elseif ($to == 'MENU_ID') {
                        $where_left = (int)$menu_id;

                    } elseif ($to == 'CATALOG_TYPE_ID') {
                        $where_left = (int)$catalog_type_id;

                    } elseif (is_numeric($to)) {
                        $where_left = (int)$to;

                    } else {

                        $has_alias = explode('.', $to);

                        if (count($has_alias) > 1) {
                            $to_join = trim($has_alias[0]) . '.' . trim($has_alias[1]);
                        } else {
                            $to_join = trim($alias) . '.' . trim($to);
                        }

                        $where_left = $to_join;
                    }

                    /** join this to THAT */
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

                    if ($with == 'APPLICATION_ID') {
                        if ((int)$this->application_id === 0) {
                            $where_right = null;
                            $to          = null;
                        } else {
                            $where_right = $this->application_id;
                        }

                    } elseif ($with == 'SITE_ID') {
                        $where_right = $this->site_id;

                    } elseif ($with == 'MENU_ID') {
                        $where_right = (int)$menu_id;

                    } elseif ($with == 'CATALOG_TYPE_ID') {
                        $where_right = (int)$catalog_type_id;

                    } elseif (is_numeric($with)) {
                        $where_right = (int)$with;

                    } else {

                        $has_alias = explode('.', $with);

                        if (count($has_alias) > 1) {
                            $with_join = trim($has_alias[0]) . '.' . trim($has_alias[1]);
                        } else {
                            $with_join = trim($primary_prefix) . '.' . trim($with);
                        }

                        $where_right = $with_join;
                    }

                    /** put the where together */
                    if ($where_left === null || $where_right === null) {
                    } else {
                        $this->query->where($where_left . $operator . $where_right);
                    }

                    $i ++;
                }
            }
        }

        if (isset($this->model_registry['criteria'])) {

            $criteria = $this->model_registry['criteria'];

            if (is_array($criteria) && count($criteria) > 0) {

                foreach ($criteria as $where) {

                    if (isset($where['name'])
                        && isset($where['connector'])
                        && isset($where['name2'])
                    ) {

                        $this->query->where(
                            $where['name']
                            . $where['connector']
                            . $where['name2']
                        );
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Add Model Registry Criteria to Query
     *
     * @return  $this
     * @since   1.0
     */
    protected function setModelCriteria()
    {
        if (isset($this->model_registry['primary_prefix'])) {
            $primary_prefix = $this->model_registry['primary_prefix'];
        } else {
            $primary_prefix = 'a';
        }

        if (isset($this->model_registry['criteria_catalog_type_id'])) {
            if ((int)$this->model_registry['criteria_catalog_type_id'] === 0) {
            } else {
                $this->query->where(
                    $primary_prefix
                    . '.'
                    . 'catalog_type_id'
                    . ' = '
                    . (int)$this->model_registry['criteria_catalog_type_id']
                );
            }
        }

        if (isset($this->model_registry['criteria_extension_instance_id'])) {
            if ((int)$this->model_registry['criteria_extension_instance_id'] === 0) {
            } else {
                $this->query->where(
                    $primary_prefix
                    . '.'
                    . 'extension_instance_id'
                    . ' = '
                    . (int)$this->model_registry['criteria_extension_instance_id']
                );
            }
        }

        if (isset($this->model_registry['criteria_menu_id'])) {
            if ((int)$this->model_registry['criteria_menu_id'] === 0) {
            } else {
                $this->query->where(
                    $primary_prefix
                    . '.'
                    . 'menu_id'
                    . ' = '
                    . (int)$this->model_registry['criteria_menu_id']
                );
            }
        }

        if (isset($this->model_registry['criteria_status'])) {
            if ((int)$this->model_registry['criteria_status'] === null) {
            } else {
                $this->query->where(
                    $primary_prefix
                    . '.'
                    . 'status'
                    . ' IN ('
                    . (string)$this->model_registry['criteria_status']
                    . ')'
                );
            }
        }

        return $this;
    }
}
