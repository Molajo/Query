<?php
/**
 * Sqllite Query Adapter
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Adapter;

use CommonApi\Query\QueryInterface;
use Molajo\Query\Builder\Sql;

/**
 * Sqllite Query Handler
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class Sqllite extends Sql implements QueryInterface
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

    /**
     * Escape the value
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0.0
     */
    public function escape($value)
    {
        return $this->quote_value . $value . $this->quote_value;
    }

    /**
     * Escape the name value
     *
     * @param   string $name
     *
     * @return  string
     * @since   1.0.0
     */
    public function escapeName($name)
    {
        return $this->name_quote_start . $name . $this->name_quote_end;
    }
}
