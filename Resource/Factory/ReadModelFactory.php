<?php
/**
 * Read Model Factory
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Resource\Factory;

use Exception;
use CommonApi\Database\DatabaseInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Read Model Factory
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ReadModelFactory
{
    /**
     * Database Instance
     *
     * @var    object   CommonApi\Database\DatabaseInterface
     * @since  1.0
     */
    public $database = null;

    /**
     * Constructor
     *
     * @param  DatabaseInterface $database
     *
     * @since  1.0
     */
    public function __construct(
        DatabaseInterface $database
    ) {
        $this->database = $database;
    }

    /**
     * Create Model Instance
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $class = 'Molajo\\Model\\ReadModel';

        try {
            return new $class (
                $this->database
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                'Resource Query Handler Failed Instantiating Model: '
                . $e->getMessage()
            );
        }
    }
}
