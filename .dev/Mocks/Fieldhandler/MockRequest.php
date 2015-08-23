<?php
/**
 * Fieldhandler Request
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Fieldhandler;

use CommonApi\Exception\UnexpectedValueException;
use CommonApi\Fieldhandler\FieldhandlerInterface;
use CommonApi\Query\FormatInterface;
use CommonApi\Query\SanitizeInterface;
use CommonApi\Query\ValidateInterface;

/**
 * The Fieldhandler Request Class is the only entry point for application access, acting as a
 *  proxy for `validate`, `sanitize`, and `format` constraints requests.
 *
 * @code
 *
 * The $request object can be used repeatedly for any `validate`, `sanitize`, and `format` requests.
 *
 * ```php
 *
 * $request = new \Molajo\Fieldhandler\Request();
 *
 * ```
 * There are three methods:
 *
 * * validate - evaluates data given constraint criteria returning true or false result and error messages
 * * filter - cleans field value not in compliance with constraint, returning new value and indicator of change
 * * format - processes field according to constraint requirements, returning new value and indicator of change
 *
 * Each method  `validate`, `sanitize`, and `format` use these four parameters:
 *
 * * $field_name - a value to be used in error messages as the name of the field
 * * $field_value - data value to be validated, filtered, or escaped
 * * $constraint - the name of the constraint to be used by this request
 * * $options - associative array of key values pairs; requirements defined by constraint class
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @api
 */
class MockRequest implements ValidateInterface, SanitizeInterface, FormatInterface, FieldhandlerInterface
{
    /**
     * Method: validate, sanitize, format
     *
     * @api
     * @var    string
     * @since  1.0.0
     */
    protected $method;

    /**
     * Field Name: value to be used in error messages as the name of the field
     *
     * @api
     * @var    string
     * @since  1.0.0
     */
    protected $field_name;

    /**
     * Field Value: data value to be validated, filtered, or escaped
     *
     * @api
     * @var    mixed
     * @since  1.0.0
     */
    protected $field_value;

    /**
     * Constraint: the name of the constraint to be used by this request
     *
     * @api
     * @var    string
     * @since  1.0.0
     */
    protected $constraint;

    /**
     * Options: associative array of key values pairs; requirements defined by constraint class
     *
     * @api
     * @var    array
     * @since  1.0.0
     */
    protected $options = array();

    /**
     * Constraint Instance
     *
     * @var    object  CommonApi\Query\ConstraintInterface
     * @since  1.0.0
     */
    protected $constraint_instance;

    /**
     * Validate Response
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $validate_response;

    /**
     * Message Instance
     *
     * @var    object  CommonApi\Query\MessageInterface
     * @since  1.0.0
     */
    protected $message_instance;

    /**
     * Message Templates
     *
     * @api
     * @var    array
     * @since  1.0.0
     */
    protected $message_templates
        = array(
            1000  => 'Field: {field_name} does not have a valid value for {constraint} data type.',
            2000  => 'Field: {field_name} must only contain {constraint} values.',
            3000  => 'Field: {field_name} is not an array.',
            4000  => 'Field: {field_name} has an invalid array element value.',
            5000  => 'Field: {field_name} has an invalid array key entry.',
            6000  => 'Field: {field_name} does not have the correct number of array values.',
            7000  => 'Field: {field_name} does not have a default value.',
            8000  => 'Field: {field_name} did not pass the {constraint} data type test.',
            9000  => 'Field: {field_name} does not have a valid file extension.',
            10000 => 'Field: {field_name} exceeded maximum value allowed.',
            11000 => 'Field: {field_name} is less than the minimum value allowed.',
            12000 => 'Field: {field_name} does not have a valid mime type.',
            13000 => 'Field: {field_name} value is required, but was not provided.',
            14000 => 'Field: {field_name} value does not match a value from the list allowed.',
        );

    /**
     * Constructor
     *
     * @param   array $message_templates
     *
     * @since   1.0.0
     */
    public function __construct(
        array $message_templates = array()
    ) {
        if (count($message_templates) > 0) {
            $this->message_templates = $message_templates;
        }
    }

    /**
     * Validate Request - validates $field_value for compliance with constraint specifications
     *
     * @param   string $field_name  Defines a textual value used in messages
     * @param   mixed  $field_value Value of the field to be processed
     * @param   string $constraint  Name of the data constraint to evaluate $field_value
     * @param   array  $options     Options vary by and are documented in the constraint class
     *
     * @code
     *
     * Validate evaluates the $field_value for the specified $constraint, given any $options
     *  the $constraint requires, and determines if the data is compliant.
     *
     * The response object contains the results of the validation request and an array of
     *  error messages if issues were found.
     *
     * ```php
     *
     * $response = $request->validate($field_name, $field_value, $constraint, $options);
     *
     * if ($response->getValidateResponse() === true) {
     *     // all is well
     * } else {
     *      foreach ($response->getValidateMessages as $code => $message) {
     *          echo $code . ': ' . $message . '/n';
     *      }
     * }
     *
     * ```
     * @api
     * @return  \CommonApi\Query\HandleResponseInterface
     * @since   1.0.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    public function validate($field_name, $field_value, $constraint, array $options = array())
    {
        return true;
    }

    /**
     * Sanitize Request - sanitizes $field_value, if necessary, in accordance with constraint specifications
     *
     * @param   string $field_name  Defines a textual value used in messages
     * @param   mixed  $field_value Value of the field to be processed
     * @param   string $constraint  Name of the data constraint to evaluate $field_value
     * @param   array  $options     Options vary by and are documented in the constraint class
     *
     * @code
     *
     * Sanitize cleans the $field_value for the specified $constraint, given any $options
     *  the $constraint requires
     *
     * The response object contains the data value following the process and a change indicator
     *
     * ```php
     *
     * $response = $request->sanitize($field_name, $field_value, $constraint, $options);
     *
     * // Replace the existing value if it was changed by filtering
     *
     * if ($response->getChangeIndicator() === true) {
     *     $field_value = $response->getFieldValue();
     * }
     *
     * ```
     * @api
     * @return  \CommonApi\Query\HandleResponseInterface
     * @since   1.0.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    public function sanitize($field_name, $field_value, $constraint, array $options = array())
    {
        $this->field_value = $field_value;

        return $this;
    }

    public function getFieldValue()
    {
        return $this->field_value;
    }

    /**
     * Format Request - formatting or special treatment defined within constraint specifications
     *
     * $response = $request->format($field_name, $field_value, $constraint, $options);
     *
     * @param   string $field_name  Defines a textual value used in messages
     * @param   mixed  $field_value Value of the field to be processed
     * @param   string $constraint  Name of the data constraint to evaluate $field_value
     * @param   array  $options     Options vary by and are documented in the constraint class
     *
     * @code
     *
     * Format processes the $field_value for the specified $constraint, given any $options
     *  the $constraint requires. Example of formatting could include email obfuscation or
     *  formatting a phone number for display.
     *
     * The response object contains the data value following the process and a change indicator
     *
     * ```php
     *
     * $response = $request->format($field_name, $field_value, $constraint, $options);
     *
     * // Replace the existing value if it was changed by the request
     *
     * if ($response->getChangeIndicator() === true) {
     *     $field_value = $response->getFieldValue();
     * }
     *
     * ```
     * @api
     * @return  \CommonApi\Query\HandleResponseInterface
     * @since   1.0.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    public function format($field_name, $field_value, $constraint, array $options = array())
    {
        return $field_value;
    }
}
