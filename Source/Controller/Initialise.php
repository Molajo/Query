<?php
/**
 * Initialise Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Query\InitialiseControllerInterface;
use stdClass;

/**
 * Initialise Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Initialise extends Read implements InitialiseControllerInterface
{
    /**
     * Initialise Row - Set Default Values for Fields
     *
     * Note: to set default values for customfieldgroups
     *  set 'process_events' to 1 in the model registry and execute
     *  the executeOnAfterReadEvent method following initialiseRow
     *  the CustomFields plugin will set defaults using content,
     *  extension and application defaults (in that order)
     *
     * @return  object
     * @since   1.0.0
     */
    public function initialiseRow()
    {
        $this->row = new stdClass();

        $this->triggerOnBeforeInitialiseEvent();

        $fields = $this->query->getModelRegistry('fields');

        if (count($fields) === 0) {
            return $this->row;
        }

        foreach ($fields as $key => $field) {

            if (isset($field['default'])) {
                $this->row->$key = $field['default'];
            } else {
                $this->row->$key = null;
            }
        }

        $this->triggerOnAfterInitialiseEvent();

        return $this->row;
    }

    /**
     * Run the onAfterReadRowEvent
     *
     * @param   object $row
     *
     * Can be used to create derived fields used in edit form for new item
     * Also, establishes default values for customfields
     *
     * @return  object
     * @since   1.0.0
     */
    public function executeOnAfterReadEvent($row)
    {
        $this->row             = $row;
        $this->query_results[] = $this->row;

        $this->triggerOnAfterReadEvent();

        return $this->query_results[0];
    }

    /**
     * Schedule onBeforeInitialise Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnBeforeInitialiseEvent()
    {
        return $this->triggerEvent('onBeforeInitialise');
    }

    /**
     * Schedule onAfterInitialise Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function triggerOnAfterInitialiseEvent()
    {
        return $this->triggerEvent('onAfterInitialise');
    }
}
