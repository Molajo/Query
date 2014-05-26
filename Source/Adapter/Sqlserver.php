<?php
/**
 * Sqlserver Query Adapter
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Adapter;

use CommonApi\Database\DatabaseInterface;
use CommonApi\Model\FieldhandlerInterface;
use CommonApi\Query\QueryInterface;

/**
 * Sqlserver Query Handler
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class Sqlserver extends AbstractConstruct implements QueryInterface
{
    /**
     * Constructor
     *
     * @since  1.0
     */
    public function __construct(
        FieldhandlerInterface $fieldhandler,
        $database_prefix = '',
        DatabaseInterface $database
    ) {
        $this->name_quote_start = '[';
        $this->name_quote_end   = ']';
        $this->date_format      = 'Y-m-d H:i:s';
        $this->null_date        = '0000-00-00 00:00:00';
        $this->quote_value      = '"';

        parent::__construct($fieldhandler, $database_prefix, $database);
    }
}
