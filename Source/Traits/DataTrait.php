<?php
/**
 * Data Trait
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query;

use Exception;
use CommonApi\Exception\RuntimeException;

/**
 * Data Trait
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
trait DataTrait
{
    /**
     * Database Adapter
     *
     * @var     object  CommonApi\Query\DatabaseInterface
     * @since   1.0.0
     */
    protected $database;

    /**
     * Query the database and return a single value as the result
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0.0
     */
    public function loadResult($sql)
    {
        try {
            return $this->database->loadResult($sql);
        } catch (Exception $e) {

            throw new RuntimeException('Database Adapter loadResult Exception: ' . $e->getMessage());
        }
    }

    /**
     * Query the database and return an array of object values returned from query
     *
     * @param   string $sql
     *
     * @return  array
     * @since   1.0.0
     */
    public function loadObjectList($sql)
    {
        try {
            return $this->database->loadObjectList($sql);

        } catch (Exception $e) {

            throw new RuntimeException('Database Adapter loadObjectList Exception: ' . $e->getMessage());
        }
    }

    /**
     * Execute the Database Query
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0.0
     */
    public function execute($sql)
    {
        try {
            return $this->database->execute($sql);

        } catch (Exception $e) {

            throw new RuntimeException('Database Adapter execute Exception: ' . $e->getMessage());
        }
    }

    /**
     * Returns the primary key following insert
     *
     * @return  integer
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getInsertId()
    {
        try {
            return $this->database->getInsertId();
        } catch (Exception $e) {

            throw new RuntimeException('Database Adapter getInsertId Exception: ' . $e->getMessage());
        }
    }
}
