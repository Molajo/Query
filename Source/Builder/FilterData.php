<?php
/**
 * Query Builder Filters
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Query\Builder;

use Exception;
use CommonApi\Exception\RuntimeException;

/**
 * Query Builder Filters
 *
 * Sql - BuildSql - BuildSqlGroups - BuildSqlElements - SetData - EditData - FilterData - Base
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class FilterData extends Base
{
    /**
     * Quote Value
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     */
    protected function quoteValue($value)
    {
        return $this->quote_value . $value . $this->quote_value;
    }

    /**
     * Quote Name
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     */
    protected function quoteNameAndPrefix($value, $prefix = null)
    {
        if ($prefix === null || trim($prefix) === '') {
            $return_prefix = '';

        } else {
            $prefix        = $this->quoteName($prefix);
            $return_prefix = $prefix . '.';
        }

        $value = $this->quoteName($value);

        return $return_prefix . $value;
    }

    /**
     * Quote Name
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     */
    protected function quoteName($value)
    {
        if (trim($value) === '*') {
            return $value;
        }

        return $this->name_quote_start . $value . $this->name_quote_end;
    }

    /**
     * Filter Input
     *
     * @param   string      $key
     * @param   null|string $value
     * @param   string      $data_type
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function filter($key, $value = null, $data_type = 'string')
    {
        if ($data_type === '') {
            $data_type = 'string';
        }

        try {
            $results = $this->fieldhandler->sanitize($key, $value, ucfirst(strtolower($data_type)));

            $value = $results->getFieldValue();

        } catch (Exception $e) {
            throw new RuntimeException(
                'Request: Filter class Failed for Key: ' . $key . ' Filter: ' . $data_type . ' ' . $e->getMessage()
            );
        }

        return $this->quoteValue($value);
    }
}
