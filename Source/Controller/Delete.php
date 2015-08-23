<?php
/**
 * Delete Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Query\DeleteControllerInterface;

/**
 * Delete Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Delete extends Cache implements DeleteControllerInterface
{
    /**
     * Method to get retrieve data
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function deleteData()
    {
        $this->triggerOnBeforeDeleteEvent();

        $this->executeQuery();

        $this->triggerOnAfterDeleteEvent();

        return $this->query_results;
    }

    /**
     * Execute the Query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function executeQuery()
    {
        $this->query_results = $this->model->deleteData($this->sql);

        return $this;
    }

    /**
     * Schedule onBeforeDelete Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnBeforeDeleteEvent()
    {
        return $this->triggerEvent('onBeforeDelete');
    }

    /**
     * Schedule onBeforeDelete Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnAfterDeleteEvent()
    {
        return $this->triggerEvent('onAfterDelete');
    }
}
