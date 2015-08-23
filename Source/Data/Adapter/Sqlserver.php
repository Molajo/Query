<?php
/**
 * Sqlserver Database Adapter
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Data\Adapter;

use CommonApi\Query\ConnectionInterface;
use CommonApi\Query\DatabaseInterface;

/**
 * Sqlserver Database Handler
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Sqlserver extends AbstractAdapter implements DatabaseInterface, ConnectionInterface
{
    /**
     * Date Format
     *
     * @var    string
     * @since  1.0
     */
    protected $date_format = 'Y-m-d H:i:s';

    /**
     * Null Date
     *
     * @var    string
     * @since  1.0
     */
    protected $null_date = '0000-00-00 00:00:00';

    /**
     * Name quote start
     *
     * @var    string
     * @since  1.0
     */
    protected $name_quote_start = '[';

    /**
     * Name quote start
     *
     * @var    string
     * @since  1.0
     */
    protected $name_quote_end = ']';

    /**
     * Current Date
     *
     * @var    string
     * @since  1.0
     */
    protected $quote_value = '"';
}
