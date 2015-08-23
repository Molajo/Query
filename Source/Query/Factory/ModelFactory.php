<?php
/**
 * Model Factory
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Query\Factory;

use Exception;
use CommonApi\Query\DatabaseInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Resource\FactoryInterface;

/**
 * Model Factory
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ModelFactory implements FactoryInterface
{
    /**
     * Database Instance
     *
     * @var    object   CommonApi\Query\DatabaseInterface
     * @since  1.0
     */
    public $database = null;

    /**
     * Crud Type
     *
     * @var    string
     * @since  1.0
     */
    public $crud_type = null;

    /**
     * Constructor
     *
     * @param  DatabaseInterface $database
     * @param  string            $crud_type
     *
     * @since  1.0
     */
    public function __construct(
        DatabaseInterface $database,
        $crud_type = 'read'
    ) {
        $this->database  = $database;
        $this->crud_type = $crud_type;
    }

    /**
     * Create Model Instance
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $class = 'Molajo\\Model\\' . ucfirst(strtolower($this->crud_type));

        try {
            return new $class (
                $this->database
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                'Molajo\\Query\\Factory\\ModelFactory::instantiateClass Failed Instantiating: '
                . $class
                . ' '
                . $e->getMessage()
            );
        }
    }
}
