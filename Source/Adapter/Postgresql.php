<?php
/**
 * Postgresql Query Adapter
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Adapter;

use CommonApi\Query\QueryInterface;

/**
 * Postgresql Query Handler
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class Postgresql extends AbstractCollect implements QueryInterface
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
    protected $name_quote_start = '"';

    /**
     * Name quote start
     *
     * @var    string
     * @since  1.0
     */
    protected $name_quote_end = '"';

    /**
     * Current Date
     *
     * @var    string
     * @since  1.0
     */
    protected $quote_value = '"';
}
