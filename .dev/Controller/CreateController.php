<?php
/**
 * Create Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use Exception;
use CommonApi\Controller\CreateControllerInterface;

/**
 * The create controller uses model registry data and HTTP post variables to edit, filter, and save
 * data, verifying foreign key restraints, expected values, permissions, etc. In addition, the create
 * controller schedules before and after create events.
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class CreateController extends ReadController implements CreateControllerInterface
{
    /**
     * create new row
     *
     * @return bool|object
     * @since  1.0
     */
    public function execute()
    {
        /** tokens */

        if (isset($this->row->model_type)) {
        } else {
            $this->row->model_type = 'datasource';
        }
        if (isset($this->row->model_name)) {
        } else {
            return false;
        }

        $this->connect($this->row->model_type, $this->row->model_name, 'CreateModel');
        if (isset($this->row->catalog_type_id) && (int)$this->row->catalog_type_id > 0) {
        } else {
            $this->row->catalog_type_id = $this->registry->get($this->model_registry, 'catalog_type_id');
        }

        $results = $this->verifyPermissions();
        if ($results === false) {
            //error
            //return false (not yet)
        }

        parent::setPluginList('create');

        $valid = $this->onBeforeCreateEvent();
        if ($valid === false) {
            return false;
            //error
        }

        $valid = $this->checkFields();
        if ($valid === false) {
            return false;
            //error
        }

        $value = $this->checkForeignKeys();

        if ($valid === true) {

            $fields = $this->registry->get($this->model_registry, 'Fields');

            if (count($fields) == 0 || $fields === null) {
                return false;
            }

            $data = new \stdClass();
            foreach ($fields as $f) {
                foreach ($f as $key => $value) {
                    if ($key == 'name') {
                        if (isset($this->row->$value)) {
                            $data->$value = $this->row->$value;
                        } else {
                            $data->$value = null;
                        }
                    }
                }
            }

            $results = $this->model->create($data, $this->model_registry);

            if ($results === false) {
            } else {
                $data->id = $results;
                $results  = $this->onAfterCreateEvent($data);
                if ($results === false) {
                    return false;
                    //error
                }
                $results = $data->id;
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
            if ($this->get('redirect_on_failure', '') == '') {
            } else {
                Services::Redirect()->url
                    = Services::Url()->get(null, null, $this->get('redirect_on_failure', '', 'parameters'));
                Services::Redirect()->code == 303;
            }
        }

        return $results;
    }

    /**
     * verifyPermissions for Create
     *
     * @return bool
     * @since  1.0
     */
    protected function verifyPermissions()
    {

        if (isset($this->row->primary_category_id)) {
            $results = $this->authorisation_controller->verifyActionPermissions(
                'Create',
                $this->row->primary_category_id
            );
            if ($results === true) {
                return true;
            }
        }

        $results = $this->authorisation_controller->verifyActionPermissions('Create', $this->row->catalog_type_id);
        if ($results === false) {
            //error
            //return false (not yet)
        }

        return true;
    }

    /**
     * checkFields
     *
     * Runs custom validation methods
     *
     * @return  boolean
     * @since   1.0
     */
    protected function checkFields()
    {

        $userHTMLFilter = $this->authorisation_controller->isUserAuthorisedNoFiltering($this->user->groups);

        /** Custom Field Groups */
        $customfieldgroups = $this->registry->get(
            $this->model_registry,
            'customfieldgroups',
            array()
        );

        if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {

            foreach ($customfieldgroups as $customFieldName) {

                /** For this Custom Field Group (ex. Parameters, metadata, etc.) */
                $customFieldName = strtolower($customFieldName);
                if (isset($this->row->$customFieldName)) {
                } else {
                    $this->row->$customFieldName = '';
                }

                /** Retrieve Field Definitions from Registry (XML) */
                $fields = $this->registry->get($this->model_registry, $customFieldName);

                /** Shared processing  */
                $valid = $this->processFieldGroup($fields, $userHTMLFilter, $customFieldName);

                if ($valid === true) {
                } else {
                    return false;
                }
            }
        }

        /** Standard Field Group */
        $fields = $this->registry->get($this->model_registry, 'Fields');
        if (count($fields) == 0 || $fields === null) {
            return false;
        }

        $valid = $this->processFieldGroup($fields, $userHTMLFilter, '');
        if ($valid === true) {
        } else {
            return false;
        }

        $this->profiler->set('message', 'CreateController::checkFields Filter::Success: ' . $valid, 'Actions');

        return $valid;
    }

    /**
     * processFieldGroup - runs custom filtering, defaults, validation for a field group
     *
     * @param        $fields
     * @param        $userHTMLFilter
     * @param string $customFieldName
     *
     * @return null|boolean
     * @since  1.0
     */
    protected function processFieldGroup($fields, $userHTMLFilter, $customFieldName = '')
    {
        $valid = true;

        if ($customFieldName == '') {
        } else {
            $fieldArray = array();
            $inputArray = array();
            $inputArray = $this->row->$customFieldName;
        }

        foreach ($fields as $f) {

            if (isset($f['name'])) {
                $name = $f['name'];
            } else {
                return false;
                //error
            }

            if (isset($f['type'])) {
                $type = $f['type'];
            } else {
                $type = null;
            }

            if (isset($f['null'])) {
                $null = $f['null'];
            } else {
                $null = null;
            }

            if (isset($f['default'])) {
                $default = $f['default'];
            } else {
                $default = null;
            }

            if (isset($f['identity'])) {
                $identity = $f['identity'];
            } else {
                $identity = 0;
            }

            /** Retrieve value from data */
            if ($customFieldName == '') {

                if (isset($this->row->$name)) {
                    $value = $this->row->$name;
                } else {
                    $value = null;
                }
            } else {

                if (isset($inputArray[$name])) {
                    $value = $inputArray[$name];
                } else {
                    $value = null;
                }
            }

            if ($type == null || $type == 'customfield' || $type == 'list') {
            } elseif ($type == 'text' && $userHTMLFilter === false) {
            } elseif ($identity == '1') {
            } else {

                try {
                    /** Filters, sets defaults, and validates */
                    $value = $this->fieldhandler->sanitize($name, $value, $type);

                    if ($customFieldName == '') {
                        $this->row->$name = trim($value);
                    } else {

                        $fieldArray[$name] = trim($value);
                    }
                } catch (Exception $e) {

                    echo 'CreateController::checkFields Filter Failed ';
                    echo 'Fieldname: ' . $name . ' Value: ' . $value . ' Type: ' . $type . ' null: ' . $null
                        . ' Default: ' . $default . '<br /> ';
                    die;
                }
            }
        }

        if ($customFieldName == '') {
        } else {
            ksort($fieldArray);
            $this->row->$customFieldName = $fieldArray;
        }

        $this->profiler->set('message', 'CreateController::checkFields Filter::Success: ' . $valid, 'Actions');

        return $valid;
    }

    /**
     * checkForeignKeys - validates the existence of all foreign keys
     *
     * @return  boolean|null
     * @since   1.0
     */
    protected function checkForeignKeys()
    {
        $foreignkeys = $this->registry->get($this->model_registry, 'foreignkeys');

        if (count($foreignkeys) == 0 || $foreignkeys === null) {
            return false;
        }

        $valid = true;

        foreach ($foreignkeys as $fk) {

            /** Retrieve Model Foreign Key Definitions */
            if (isset($fk['name'])) {
                $name = $fk['name'];
            } else {
                return false;
                //error
            }
            if (isset($fk['source_id'])) {
                $source_id = $fk['source_id'];
            } else {
                return false;
                //error
            }

            if (isset($fk['source_model'])) {
                $source_model = ucfirst(strtolower($fk['source_model']));
            } else {
                return false;
                //error
            }

            if (isset($fk['required'])) {
                $required = $fk['required'];
            } else {
                return false;
                //error
            }

            /** Retrieve Model Foreign Key Definitions */
            if (isset($this->row->$name)) {
            } else {
                if ((int)$required == 0) {
                    return true;
                }

                // error
                return false;
            }

            if (isset($this->row->$name)) {

                $controller_class_namespace = $this->controller_namespace;
                $controller                 = new $controller_class_namespace();
                $controller->getModelRegistry('datasource', $source_model, 1);

                $controller->select('COUNT(*)');
                $controller->from($controller->get('table_name'));
                $controller->where(
                    $source_id
                    . ' = '
                    . (int)$this->row->$name
                );

                $controller->set('get_customfields', 0);
                $controller->set('get_item_children', 0);
                $controller->set('use_special_joins', 0);
                $controller->set('check_view_level_access', 0);
                $controller->set('process_events', 0);

                $value = $controller->getData('result');

                if (empty($value)) {
                    //error
                    return false;
                }
            } else {
                if ($required == 0) {
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * Schedule Event onBeforeCreateEvent Event - could update model and data objects
     *
     * @return boolean
     * @since   1.0
     */
    protected function xonBeforeCreateEvent()
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
            'CreateController->onBeforeCreateEvent Schedules onBeforeCreate',
            'Plugins',
            1
        );

        $arguments = $this->event->schedule_event('onBeforeCreate', $arguments, $this->plugins);

        if ($arguments === false) {
            $this->profiler->set('message', 'CreateController->onBeforeCreateEvent failed.', 'Plugins', 1);

            return false;
        }

        $this->profiler->set('message', 'CreateController->onBeforeCreateEvent successful.', 'Plugins', 1);

        $this->parameters = $arguments['parameters'];
        $this->row        = $arguments['row'];

        return true;
    }

    /**
     * Schedule Event onAfterCreateEvent Event
     *
     * @param   $data
     *
     * @return boolean
     * @since   1.0
     */
    protected function xonAfterCreateEvent($data)
    {
        if (count($this->plugins) == 0
            || (int)$this->get('process_events') == 0
        ) {
            return true;
        }

        /** Schedule Event onAfterCreate Event */
        $arguments = array(
            'model_registry' => $this->model_registry,
            'db'             => $this->model->database,
            'data'           => $data,
            'parameters'     => $this->parameters,
            'model_type'     => $this->get('model_type'),
            'model_name'     => $this->get('model_name')
        );

        $this->profiler->set(
            'message',
            'CreateController->onAfterCreateEvent Schedules onAfterCreate',
            'Plugins',
            1
        );

        $arguments = $this->event->schedule_event('onAfterCreate', $arguments, $this->plugins);

        if ($arguments === false) {
            $this->profiler->set('message', 'CreateController->onAfterCreateEvent failed.', 'Plugins', 1);

            return false;
        }

        $this->profiler->set('message', 'CreateController->onAfterCreateEvent successful.', 'Plugins', 1);

        $this->parameters = $arguments['parameters'];
        $data             = $arguments['row'];

        return $data;
    }
}
