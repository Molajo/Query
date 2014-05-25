<?php
/**
 * Form Controller
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller;

use stdClass;

/**
 * Form Controller
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Form
{
    /**
     * Form Sections
     *
     * @var    array
     * @since  1.0
     */
    protected $form_sections = array();

    /**
     * Sections
     *
     * @var    array
     * @since  1.0
     */
    protected $form_section_fieldsets = array();

    /**
     * Fields
     *
     * @var    array
     * @since  1.0
     */
    protected $form_section_fieldset_fields = array();

    /**
     * Stores Form Sections
     *
     * @param   string $section_array
     *
     * @return  $this
     * @since   1.0
     */
    public function setFormSections($section_array)
    {
        $temp          = explode('{{', $section_array);
        $form_sections = array();
        foreach ($temp as $section) {
            $x = substr($section, 0, strlen($section) - 2);
            if ($x === false) {
            } else {
                $y                    = explode(',', $x);
                $form_sections[$y[0]] = $y[1];
            }
        }

        $section_array = array();
        $active        = ' active';
        foreach ($form_sections as $key => $value) {
            $row                 = new stdClass();
            $row->template_label = $key;
            $row->template_view  = $value;
            $row->active         = $active;
            $section_array[]     = $row;
            $active              = '';
        }

        $this->form_sections = $section_array;

        return $this;
    }

    /**
     * Stores Form Section Fieldsets
     *
     * @param   object $parameters
     *
     * @return  array
     * @since   1.0
     */
    public function setFormSectionFieldsets($parameters)
    {
        $this->form_section_fieldsets = array();

        foreach ($this->form_sections as $item) {

            $field                  = $item->template_view;
            $y                      = strtolower($field);
            $form_section_fieldsets = array();

            if (isset($parameters->$y)) {

                $x    = $parameters->$y;
                $temp = explode('{{', $x);

                foreach ($temp as $section) {

                    $x = substr($section, 0, strlen($section) - 2);

                    if (trim($x) == '') {
                    } else {
                        $y                             = explode(',', $x);
                        $item->template_view           = ucfirst(strtolower($item->template_view));
                        $row                           = new stdClass();
                        $row->template_label           = $item->template_label;
                        $row->template_view            = $item->template_view;
                        $row->field_masks              = $y[1];
                        $form_section_fieldsets[$y[1]] = $row;
                    }
                }
            }

            $this->form_section_fieldsets[$item->template_view] = $form_section_fieldsets;

        }

        return $this->form_section_fieldsets;
    }

    /**
     * Extract Form Fields
     *
     * @param   object $parameters
     * @param   object $model_registry
     * @param   bool   $map_to_actual
     *
     * @return  $this
     * @since   1.0
     */
    public function setFormFieldsetFields($parameters, $model_registry, $map_to_actual = true)
    {
        $this->form_section_fieldset_fields = array();

        $all_fields = $this->getAllFields($model_registry);

        $this->form_section_fieldset_fields = array();

        foreach ($this->form_section_fieldsets as $ignore => $items) {

            $i = 0;
            foreach ($items as $key => $item) {

                $fields = $this->getFieldsetFields($item->field_masks, $parameters, $map_to_actual);

                if (is_array($fields) && count($fields) > 0) {

                    foreach ($fields as $field) {

                        $row       = new stdClass();
                        $row->id   = $i ++;
                        $row->name = $field;

                        foreach ($item as $property => $property_value) {
                            $row->$property = $property_value;
                        }

                        foreach ($all_fields as $all_field) {

                            if ($field == $all_field->name) {
                                foreach ($all_field as $property => $property_value) {
                                    $row->$property = $property_value;
                                }
                                break;
                            }
                        }

                        $this->form_section_fieldset_fields[$row->name] = $row;
                    }
                }
            }
        }

        return $this->form_section_fieldset_fields;
    }

    /**
     * Extract Fieldset Fields
     *
     * @param   string  $field_mask
     * @param   bool    $map_to_actual
     *
     * @return  array
     * @since   1.0
     */
    public function getFieldsetFields($field_mask, $parameters, $map_to_actual = true)
    {
        $masks = explode(',', $field_mask);

        if (is_array($masks) && count($masks) > 0) {
        } else {
            return array();
        }

        $save_field = array();

        foreach ($masks as $mask) {

            $x = substr($mask, strlen($mask) - 1, 1);
            if ($x == '*') {
                $mask = substr($mask, 0, strlen($mask) - 1);
            }

            foreach ($parameters as $key => $value) {
                if (substr($key, 0, strlen($mask)) == $mask) {
                    if (isset($parameters->$key)) {
                        if ($map_to_actual === true) {
                            $selected_field = $parameters->$key;
                        } else {
                            $selected_field = $key;
                        }
                        if (trim($selected_field) == '') {
                        } elseif (substr($selected_field, 0, 2) == '{{') {
                        } else {
                            $save_field[] = $selected_field;
                        }
                    }
                }
            }
        }

        return $save_field;
    }

    /**
     * Get All Fields for Model Registry
     *
     * @param   object $model_registry
     *
     * @return  array
     * @since   1.0
     */
    public function getAllFields($model_registry)
    {
        $all_fields = array();

        $fields = $model_registry['fields'];

        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field) {
                $row = new stdClass();

                foreach ($field as $key => $value) {
                    $row->$key = $value;
                }

                $row->fieldtype = 'field';
                $all_fields[]   = $row;
            }
        }

        $customfieldgroups = $model_registry['customfieldgroups'];
        if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {
            foreach ($customfieldgroups as $customfieldgroup) {

                $fields = $model_registry[$customfieldgroup];

                if (is_array($fields) && count($fields) > 0) {
                    foreach ($fields as $field) {
                        $row = new stdClass();

                        foreach ($field as $key => $value) {
                            $row->$key = $value;
                        }

                        $row->fieldtype = $customfieldgroup;
                        $all_fields[]   = $row;
                    }
                }
            }
        }

        sort($all_fields);

        return $all_fields;
    }
}
