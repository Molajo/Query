<?php
/**
 * Read Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Query\ReadControllerInterface;

/**
 * Read Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Read extends Cache implements ReadControllerInterface
{
    /**
     * Process Rows Count
     *
     * @var    integer
     * @since  1.0
     */
    protected $process_rows_count;

    /**
     * Set SQL prior to running Query
     *
     * @param   null|string $sql
     *
     * @return  string
     * @since   1.0.0
     */
    public function setSql($sql = null)
    {
        $this->sql = $sql;

        $this->triggerOnBeforeReadEvent();

        $this->sql = $this->getSql($this->sql);

        return $this->sql;
    }

    /**
     * Method to get retrieve data
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getData()
    {
        $this->runQuery();

        $this->triggerOnAfterReadEvent();

        $this->triggerOnAfterReadallEvent();

        return $this->returnQueryResults();
    }

    /**
     * Build the SQL needed for the query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function runQuery()
    {
        $this->executeQuery();

        if ($this->use_pagination === 0 || (int)$this->total === 0) {
            return $this;
        }

        $this->processPagination($this->query_results);

        return $this;
    }

    /**
     * Execute the Query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function executeQuery()
    {
        $this->query_results = null;

        $this->getQueryCache();

        if ($this->query_results === null) {
//echo 'In Read Controller SQL: <br>';
//echo $this->sql;
//echo '<br>';
            $this->query_results = $this->model->getData(
                $this->getModelRegistry('query_object'),
                $this->sql
            );

            $this->setQueryCache();
        }

        $this->total = count($this->query_results);

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
     * @since   1.0.0
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
     * @since   1.0.0
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
     * Schedule Event onAfterReadRow Event
     *
     * @return  array
     * @since   1.0.0
     */
    protected function returnQueryResults()
    {
        if ($this->getModelRegistry('query_object') === 'result'
            || $this->getModelRegistry('query_object') === 'distinct'
        ) {
            return $this->query_results;
        }

        if ($this->getModelRegistry('query_object') === 'item') {
            if (count($this->query_results) === 0) {
                $result = null;
            } else {
                $result = $this->query_results[0];
            }
            $this->query_results = $result;
        }

        return $this->query_results;
    }

    /**
     * Schedule onBeforeRead Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnBeforeReadEvent()
    {
        return $this->triggerEvent('onBeforeRead');
    }

    /**
     * Schedule onAfterReadRow Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnAfterReadEvent()
    {
        $rows                = $this->query_results;
        $this->query_results = array();
        $first               = true;

        if (count($rows) === 0) {
        } else {

            if ($this->getModelRegistry('query_object') === 'result'
                || $this->getModelRegistry('query_object') === 'distinct'
            ) {
                $this->query_results = $rows;

                return $this;
            }

            foreach ($rows as $this->row) {
                $this->runtime_data->first = $first;
                $this->triggerEvent('onAfterReadRow');
                $first = false;
            }
        }

        unset($this->runtime_data->first);

        $this->query_results = $rows;

        return $this;
    }

    /**
     * Schedule Event onAfterReadRowAll Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnAfterReadallEvent()
    {
        return $this->triggerEvent('onAfterRead');
    }
}
