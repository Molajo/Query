<?php
/**
 * Query Adapter
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use Exception;
use CommonApi\Model\QueryInterface;
use CommonApi\Exception\UnexpectedValueException;

/**
 * Query Adapter
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0
 */
class Adapter implements QueryInterface
{
    /**
     * Query Adapter Handler
     *
     * @var     object  CommonApi\Query\QueryInterface
     * @since   1.0
     */
    protected $handler;

    /**
     * Constructor
     *
     * @param  QueryInterface $query
     *
     * @since  1.0
     */
    public function __construct(QueryInterface $query)
    {
        $this->handler = $query;
    }

    /**
     * Instantiate a Query Object for Select, Insert, Update, Delete, Execute or Call
     *
     * @param   string  $type
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\UnexpectedValueException
     */
    public function getQueryObject($type = 'select')
    {
        $type = strtolower($type);

        if ($type == 'insert') {
        } elseif ($type == 'update') {
        } elseif ($type == 'delete') {
        } elseif ($type == 'execute') {
        } elseif ($type == 'call') {
        } else {
            $type = 'select';
        }

        try {
            return $this->handler->getQueryObject($type);

        } catch (Exception $e) {

            throw new UnexpectedValueException
            ('Query Adapter getQueryObject Exception: ' . $e->getMessage());
        }
    }

    /**
     * Returns a string containing the query, resolved from the query object
     *
     * @return  string
     * @since   1.0
     * @throws  RuntimeException
     */
    public function getQueryString()
    {
        try {
            return $this->handler->getQueryString();

        } catch (Exception $e) {

            throw new RuntimeException
            ('Query Adapter getQueryString Exception: ' . $e->getMessage());
        }
    }

    /**
     * Quote Value
     *
     * @param   string $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  RuntimeException
     */
    public function quote($value)
    {
        try {
            return $this->handler->quote($value);
        } catch (Exception $e) {

            throw new RuntimeException
            ('Query Adapter quote Exception: ' . $e->getMessage());
        }
    }

    /**
     * Quote and return name
     *
     * @param   string $name
     *
     * @return  string
     * @since   1.0
     * @throws  RuntimeException
     */
    public function quoteName($name)
    {
        try {
            return $this->handler->quoteName($name);
        } catch (Exception $e) {

            throw new RuntimeException
            ('Query Adapter quoteName Exception: ' . $e->getMessage());
        }
    }

    /**
     * Method to escape a string for usage in an SQL statement.
     *
     * @param   string $text
     *
     * @return  string
     * @since   1.0
     * @throws  RuntimeException
     */
    public function escape($text)
    {
        try {
            return $this->handler->escape($text);
        } catch (Exception $e) {

            throw new RuntimeException
            ('Query Adapter escape Exception: ' . $e->getMessage());
        }
    }

    /**
     * Alias of Quote
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     * @throws  RuntimeException
     */
    public function q($value)
    {
        try {
            return $this->handler->quote($value);
        } catch (Exception $e) {

            throw new RuntimeException
            ('Query Adapter q Exception: ' . $e->getMessage());
        }
    }

    /**
     * Alias of quoteName
     *
     * @param   string $name
     *
     * @return  string
     * @since   1.0
     * @throws  RuntimeException
     */
    public function qn($name)
    {
        try {
            return $this->handler->quoteName($name);
        } catch (Exception $e) {

            throw new RuntimeException
            ('Query Adapter qn Exception: ' . $e->getMessage());
        }
    }

    /**
     * Alias of Escape
     *
     * @param   string $text
     * @param   bool   $extra
     *
     * @return  string
     * @since   1.0
     * @throws  RuntimeException
     */
    public function e($text, $extra)
    {
        try {
            return $this->handler->escape($text, $extra);
        } catch (Exception $e) {

            throw new RuntimeException
            ('Query Adapter escape Exception: ' . $e->getMessage());
        }
    }
}
