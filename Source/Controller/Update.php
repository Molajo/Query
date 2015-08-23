<?php
/**
 * Update Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Query\UpdateControllerInterface;

/**
 * Update Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Update extends Cache implements UpdateControllerInterface
{
    /**
     * Method to get retrieve data
     *
     * @return  mixed
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function updateData()
    {
        $this->triggerOnBeforeUpdateEvent();

        $this->executeQuery();

        $this->triggerOnAfterUpdateEvent();

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
        $this->query_results = $this->model->updateData($this->sql);

        return $this;
    }

    /**
     * Schedule onBeforeUpdate Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnBeforeUpdateEvent()
    {
        return $this->triggerEvent('onBeforeUpdate');
    }

    /**
     * Schedule onBeforeUpdate Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnAfterUpdateEvent()
    {
        return $this->triggerEvent('onAfterUpdate');
    }
}
