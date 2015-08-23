<?php
/**
 * Database Driver
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Data;

use CommonApi\Query\DatabaseInterface;

/**
 * Database Driver
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Driver implements DatabaseInterface
{
    /**
     * Data Trait
     *
     * @var     object  Molajo\Query\DataTrait
     * @since   1.0.0
     */
    use \Molajo\Query\DataTrait;

    /**
     * Constructor
     *
     * @param  DatabaseInterface $database
     *
     * @since  1.0
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }
}
