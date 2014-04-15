<?php
/**
 * Delete Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use CommonApi\Controller\DeleteControllerInterface;

/**
 * The delete controller uses model registry data and HTTP post variables to verifying foreign key restraints,
 * and permissions, etc, archive version history, and delete data. The delete controller also schedules the
 * before and after delete event.
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class DeleteController extends ReadController implements DeleteControllerInterface
{
    /**
     * Delete row and plugin other delete actions
     *
     * @return bool|object
     * @since  1.0
     */
    public function delete()
    {
        /** tokens */

        if (isset($this->row->model_name)) {
        } else {
            return false;
        }

        $results = $this->getDeleteData();
        if ($results === false) {
            return false;
        }

        $results = $this->verifyPermissions();
        if ($results === false) {
            //error
            //return false (not yet)
        }

        $this->setPluginList('delete');

        $valid = $this->onBeforeDeleteEvent();
        if ($valid === false) {
            return false;
            //error
        }

        if ($valid === true) {

            $this->connect('datasource', $this->row->model_name, 'DeleteModel');
            $results = $this->model->delete($this->row, $this->model_registry);

            if ($results === false) {
            } else {
                $this->row->id = $results;
                $results       = $this->onAfterDeleteEvent();
                if ($results === false) {
                    return false;
                    //error
                }
                $results = $this->row->id;
            }
        }

        /** redirect */
        if ($valid === true) {
            if ($this->get('redirect_on_success', '', 'parameters') == '') {
            } else {
                Services::Redirect()->url
                    = Services::Url()->get(null, null, $this->get('redirect_on_success', '', 'parameters'));

                Services::Redirect()->code == 303;
            }
        } else {
            if ($this->get('redirect_on_failure', '', 'parameters') == '') {
            } else {
                Services::Redirect()->url
                    = Services::Url()->get(null, null, $this->get('redirect_on_failure', '', 'parameters'));

                Services::Redirect()->code == 303;
            }
        }

        return $results;
    }

    /**
     * Retrieve data to be deleted
     *
     * @return bool|mixed
     * @since  1.0
     */
    public function getDeleteData()
    {
        $hold_model_name = $this->row->model_name;
        $this->connect('datasource', $hold_model_name);

        $this->set('use_special_joins', 0);
        $name_key       = $this->get('name_key');
        $primary_key    = $this->get('primary_key');
        $primary_prefix = $this->get('primary_prefix', 'a');

        if (isset($this->row->$primary_key)) {
            $this->query->where(
                $primary_prefix
                . '.'
                . $primary_key
                . ' = '
                . $this->row->$primary_key
            );
        } elseif (isset($this->row->$name_key)) {
            $this->query->where(
                $primary_prefix
                . '.'
                . $name_key
                . ' = '
                . $this->row->$name_key
            );
        } else {
            //only deletes single rows
            return false;
        }

        if (isset($this->row->catalog_type_id)) {
            $this->query->where(
                $primary_prefix
                . '.'
                . 'catalog_type_id'
                . ' = '
                . $this->row->catalog_type_id
            );
        }

        $item = $this->getData('item');
//		echo '<br /><br /><br />';
//		echo $this->query->__toString();
//		echo '<br /><br /><br />';

        if ($item === false) {
            //error
            return false;
        }

        $fields = $this->registry->get($this->model_registry, 'Fields');
        if (count($fields) == 0 || $fields === null) {
            return false;
        }

        $this->row = new \stdClass();
        foreach ($fields as $f) {
            foreach ($f as $key => $value) {
                if ($key == 'name') {
                    if (isset($item->$value)) {
                        $this->row->$value = $item->$value;
                    } else {
                        $this->row->$value = null;
                    }
                }
            }
        }

        if (isset($item->catalog_id)) {
            $this->row->catalog_id = $item->catalog_id;
        }
        $this->row->model_name = $hold_model_name;

        /** Process each field namespace  */
        $customFieldTypes = $this->registry->get($this->model_registry, 'customfieldgroups');

        if (count($customFieldTypes) > 0) {
            foreach ($customFieldTypes as $customFieldName) {
                $customFieldName = ucfirst(strtolower($customFieldName));
                $this->registry->merge($this->model_registry . $customFieldName, $customFieldName);
                $this->registry->deleteRegistry($this->model_registry . $customFieldName);
            }
        }

        return true;
    }

    /**
     * verifyPermissions for Delete
     *
     * @return bool
     * @since  1.0
     */
    protected function verifyPermissions()
    {
        //@todo - figure out what joining isn't working, get catalog id
        //$results = $this->authorisation_controller->verifyActionPermissions('Delete', $this->row->catalog_id);
        //if ($results === false) {
        //error
        //return false (not yet)
        //}
        return true;
    }

    /**
     * Schedule Event onBeforeDeleteEvent Event - could update model and data objects
     *
     * @return boolean
     * @since   1.0
     */
    protected function XonBeforeDeleteEvent()
    {
        if (count($this->plugins) == 0
            || (int)$this->get('process_events') == 0
        ) {
            return true;
        }

        $arguments = array(
            'model_registry' => $this->model_registry,
            'db'             => $this->model->database,
            'data'           => $this->row,
            'null_date'      => $this->model->null_date,
            'now'            => $this->model->now,
            'parameters'     => $this->parameters,
            'model_type'     => $this->get('model_type'),
            'model_name'     => $this->get('model_name')
        );

        $this->profiler->set(
            'message',
            'DeleteController->onBeforeDeleteEvent Schedules onBeforeDelete',
            'Plugins',
            1
        );

        $arguments = $this->event->schedule_event('onBeforeDelete', $arguments, $this->plugins);
        if ($arguments === false) {
            $this->profiler->set('message', 'DeleteController->onBeforeDelete failed.', 'Plugins', 1);

            return false;
        }

        $this->profiler->set('message', 'DeleteController->onBeforeDeleteEvent succeeded.', 'Plugins', 1);

        /** Process results */
        $this->parameters = $arguments['parameters'];
        $this->row        = $arguments['row'];

        return true;
    }

    /**
     * Schedule Event onAfterDeleteEvent Event
     *
     * @return boolean
     * @since   1.0
     */
    protected function XonAfterDeleteEvent()
    {
        if (count($this->plugins) == 0
            || (int)$this->get('process_events') == 0
        ) {
            return true;
        }

        /** Schedule Event onAfterDelete Event */
        $arguments = array(
            'model_registry' => $this->model_registry,
            'db'             => $this->model->database,
            'data'           => $this->row,
            'parameters'     => $this->parameters,
            'model_type'     => $this->get('model_type'),
            'model_name'     => $this->get('model_name')
        );

        $this->profiler->set(
            'message',
            'CreateController->onAfterDeleteEvent Schedules onAfterDelete',
            'Plugins',
            1
        );

        $arguments = $this->event->scheduleEvent('onAfterDelete', $arguments, $this->plugins);
        if ($arguments === false) {
            $this->profiler->set('message', 'DeleteController->onAfterDelete failed.', 'Plugins', 1);

            return false;
        }

        $this->profiler->set('message', 'DeleteController->onAfterDelete succeeded.', 'Plugins', 1);

        /** Process results */
        $this->parameters = $arguments['parameters'];
        $this->row        = $arguments['row'];

        return true;
    }
}
